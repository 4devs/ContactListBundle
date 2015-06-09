<?php

namespace FDevs\ContactListBundle\DependencyInjection\Compiler;


use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

class AddTranslatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('translator.default')) {
            return;
        }
        $translator = $container->getDefinition('translator.default');

        $refl = new \ReflectionClass('FDevs\ContactList\ContactFactory');
        $dirs = [dirname($refl->getFileName()).'/Resources/translations'];

        foreach ($dirs as $dir) {
            $container->addResource(new DirectoryResource($dir));
        }

        $files = [];
        $finder = Finder::create()
            ->files()
            ->filter(function (\SplFileInfo $file) {
                return 2 === substr_count($file->getBasename(), '.') && preg_match('/\.\w+$/', $file->getBasename());
            })
            ->in($dirs);

        foreach ($finder as $file) {
            list($domain, $locale, $format) = explode('.', $file->getBasename(), 3);
            if (!isset($files[$locale])) {
                $files[$locale] = [];
            }

            $files[$locale][] = (string)$file;
        }

        $options = array_merge_recursive(
            $translator->getArgument(3),
            ['resource_files' => $files]
        );

        $translator->replaceArgument(3, $options);
    }
}