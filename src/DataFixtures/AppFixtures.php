<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AppFixtures extends Fixture
{
    protected array $datas;

    public function setDataFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $filePath));
        }

        $this->datas = (array) Yaml::parseFile($filePath);
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->datas as $item) {
            if (!class_exists($item['className'])) {
                continue;
            }
            foreach ($item['datas'] as $id => $data) {
                $object = new $item['className']();
                $builder = PropertyAccess::createPropertyAccessorBuilder();
                $builder->disableExceptionOnInvalidPropertyPath();

                foreach ($data as $property => $fixture) {
                    if (\is_array($fixture)) {
                        if (!empty($fixture['className']) && !empty($fixture['datas'])) {
                            if (!class_exists($fixture['className'])) {
                                continue;
                            }
                            foreach ($fixture['datas'] as $subData) {
                                $subObject = $manager->find($fixture['className'], $subData);
                                $builder->getPropertyAccessor()->setValue($object, $fixture['method'] ?? $property, $subObject);
                            }
                        }
                    } else {
                        $builder->getPropertyAccessor()->setValue($object, $property, $fixture);
                    }
                }
                $manager->persist($object);
            }
            $manager->flush();
        }
    }
}
