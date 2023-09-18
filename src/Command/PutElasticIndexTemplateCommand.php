<?php


namespace ATSearchBundle\Command;

use Exception;
use OpenSearch\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ATSearchBundle\Elastic\Mapper\SchemaMapper;
use ATSearchBundle\Elastic\ValueObject\Document;

#[AsCommand(name: 'at_search:put_elastic:index_template')]
class PutElasticIndexTemplateCommand extends Command
{
    public function __construct(private readonly Client $client)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Put elastic index template - start');

        $alreadyExists = false;

        try {
            $request = $this->buildRequest(false);
            $this->client->indices()->putIndexTemplate($request);
        } catch (Exception $e) {
            if (!str_contains($e->getMessage(), 'already exists') && !str_contains($e->getMessage(), 'default')) {
                $io->error('Put elastic index template - error');
            } else {
                $alreadyExists = true;
                $io->warning('Index template already exists');
            }
        }

        if ($alreadyExists && $io->confirm('Index template already exists, overwrite?', false)) {
            $io->warning('Index template already exists, overwriting');
            $request = $this->buildRequest(true);
            $this->client->indices()->putIndexTemplate($request);
            $io->success('Index template overwritten');
        }

        $maxResultWindow = $this->client->indices()->getSettings([
            'index' => Document::$indexPrefix . '*',
        ]);

        foreach ($maxResultWindow as $key => $value) {
            $io->writeln('Current max_result_window setting for ' . $key . ': ' . ($value['settings']['index']['max_result_window'] ?? 'not set (default: 10000))'));
        }

        if (!$io->confirm('Do you want to update max_result_window setting for all indices?', false)) {
            return Command::SUCCESS;
        }

        $new = (int)$io->ask('New max_result_window setting (default: 50000)', 50000);
        try {
            $this->client->indices()->putSettings([
                'index' => Document::$indexPrefix . '*',
                'body' => [
                    'max_result_window' => $new,
                ],
            ]);
            $io->success('Max result window setting updated.');
        } catch (Exception $e) {
            $io->error('Error updating max_result_window setting.' . $e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function buildRequest(bool $overwrite): array
    {
        $dynamicTemplates = [];
        foreach (SchemaMapper::getAvailableFieldTypes() as $name => $type) {
            $element = [
                'ats_' . $name => [
                    'match' => '*' . SchemaMapper::getSuffixByCustomType($name),
                    'mapping' => [
                        'type' => $type,
                    ],
                ],
            ];
            if (in_array($name, ['string', 'mstring'])) {
                $element['ats_' . $name]['mapping']['normalizer'] = 'lowercase_normalizer';
            }
            $dynamicTemplates[] = $element;
        }

        return [
            'name' => 'default',
            'create' => !$overwrite,
            'body' => [
                'index_patterns' => [
                    Document::$indexPrefix . '*',
                ],
                'template' => [
                    'settings' => [
                        'analysis' => [
                            'normalizer' => [
                                'lowercase_normalizer' => [
                                    'type' => 'custom',
                                    'char_filter' => [],
                                    'filter' => [
                                        'lowercase',
                                    ],
                                ],
                            ],
                        ],
                        'refresh_interval' => '-1',
                    ],
                    'mappings' => [
                        'dynamic_templates' => $dynamicTemplates,
                    ],
                ],
            ],
        ];
    }

}