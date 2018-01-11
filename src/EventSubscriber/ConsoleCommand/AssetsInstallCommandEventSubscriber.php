<?php

namespace App\EventSubscriber\ConsoleCommand;

use Gaufrette\Filesystem;
use Symfony\Bundle\FrameworkBundle\Command\AssetsInstallCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetsInstallCommandEventSubscriber implements EventSubscriberInterface
{
    /**  @var Filesystem */
    private $cdnFileSystem;

    private $manifestFilename;

    private $publicDir;

    private $isDebug;

    public function __construct(Filesystem $cdnFileSystem, string $manifestFilename, string $publicDir, bool $isDebug)
    {
        $this->cdnFileSystem = $cdnFileSystem;
        $this->manifestFilename = $manifestFilename;
        $this->publicDir = $publicDir;
        $this->isDebug = $isDebug;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleCommand',
        ];
    }

    /**
     * @param ConsoleCommandEvent $event
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Gaufrette\Exception\FileAlreadyExists
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $command = $event->getCommand();
        if (!$command instanceof AssetsInstallCommand) {
            return;
        }

        if ($this->isDebug) {
            return;
        }

        $manifest = file_get_contents($this->manifestFilename);
        $files = json_decode($manifest, true);
        foreach ((array)$files as $file) {
            $this->copyAssetToCdn($file);
        }
    }

    /**
     * @param string $filename
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Gaufrette\Exception\FileAlreadyExists
     */
    private function copyAssetToCdn(string $filename)
    {
        $fullAssetFilename = sprintf('%s%s', $this->publicDir, $filename);
        $assetContent = file_get_contents($fullAssetFilename);
        $this->cdnFileSystem->write($filename, $assetContent, true);
    }
}

