<?php

declare(strict_types=1);

namespace Stadly\FileWaiter\Adapter;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Stadly\FileWaiter\Adapter;
use Stadly\FileWaiter\Exception\FileCouldNotBeFound;
use Stadly\FileWaiter\Exception\StreamCouldNotBeOpened;
use Stadly\Http\Header\Value\Date;
use Stadly\Http\Header\Value\EntityTag\EntityTag;
use Stadly\Http\Header\Value\MediaType\MediaType;

/**
 * Adapter for handling files stored in the abstract file system Flysystem: http://flysystem.thephpleague.com
 */
final class Flysystem implements Adapter
{
    /**
     * @var FilesystemInterface File system where the file is stored.
     */
    private $fileSystem;

    /**
     * @var string Path to the file in the file system.
     */
    private $filePath;

    /**
     * @var StreamFactoryInterface Factory for creating streams.
     */
    private $streamFactory;

    /**
     * Constructor.
     *
     * @param FilesystemInterface $fileSystem File system where the file is stored.
     * @param string $filePath Path to the file in the file system.
     * @param StreamFactoryInterface $streamFactory Factory for creating streams.
     * @throws FileCouldNotBeFound If the file could not be found.
     */
    public function __construct(
        FilesystemInterface $fileSystem,
        string $filePath,
        StreamFactoryInterface $streamFactory
    ) {
        if (!$fileSystem->has($filePath)) {
            throw new FileCouldNotBeFound($filePath);
        }

        try {
            $metadata = $fileSystem->getMetadata($filePath);
        // @codeCoverageIgnoreStart
        } catch (FileNotFoundException $exception) {
            throw new FileCouldNotBeFound($filePath, $exception);
        }
        // @codeCoverageIgnoreEnd

        if ($metadata === false || $metadata['type'] !== 'file') {
            throw new FileCouldNotBeFound($filePath);
        }

        $this->fileSystem = $fileSystem;
        $this->filePath = $filePath;
        $this->streamFactory = $streamFactory;
    }

    /**
     * @inheritdoc
     */
    public function getFileStream(): StreamInterface
    {
        try {
            $fileStream = $this->fileSystem->readStream($this->filePath);
        } catch (FileNotFoundException $exception) {
            throw new StreamCouldNotBeOpened($this->filePath, $exception);
        }

        if ($fileStream === false) {
            // @codeCoverageIgnoreStart
            throw new StreamCouldNotBeOpened($this->filePath);
            // @codeCoverageIgnoreEnd
        }

        return $this->streamFactory->createStreamFromResource($fileStream);
    }

    /**
     * @inheritdoc
     */
    public function getFileName(): string
    {
        return basename($this->filePath);
    }

    /**
     * @inheritdoc
     */
    public function getFileSize(): ?int
    {
        try {
            $fileSize = $this->fileSystem->getSize($this->filePath);
        } catch (FileNotFoundException $exception) {
            return null;
        }

        if ($fileSize === false) {
            return null;
        }

        return $fileSize;
    }

    /**
     * @inheritdoc
     */
    public function getMediaType(): ?MediaType
    {
        try {
            $mediaTypeString = $this->fileSystem->getMimetype($this->filePath);
        } catch (FileNotFoundException $exception) {
            return null;
        }

        if ($mediaTypeString === false || $mediaTypeString === 'directory') {
            return null;
        }

        return MediaType::fromString($mediaTypeString);
    }

    /**
     * @inheritdoc
     */
    public function getLastModifiedDate(): ?Date
    {
        try {
            $timestamp = $this->fileSystem->getTimestamp($this->filePath);
        } catch (FileNotFoundException $exception) {
            return null;
        }

        if ($timestamp === false) {
            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        return Date::fromTimestamp($timestamp);
    }

    /**
     * @inheritdoc
     */
    public function getEntityTag(): ?EntityTag
    {
        try {
            $content = $this->fileSystem->read($this->filePath);
        } catch (FileNotFoundException $exception) {
            return null;
        }

        if ($content === false) {
            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        return new EntityTag(md5($content));
    }
}
