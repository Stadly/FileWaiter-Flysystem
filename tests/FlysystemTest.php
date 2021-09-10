<?php

declare(strict_types=1);

namespace Stadly\FileWaiter\Adapter;

use GuzzleHttp\Psr7\HttpFactory;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Stadly\FileWaiter\Exception\FileCouldNotBeFound;
use Stadly\FileWaiter\Exception\StreamCouldNotBeOpened;
use Stadly\Http\Header\Value\Date;
use Stadly\Http\Header\Value\EntityTag\EntityTag;
use Stadly\Http\Header\Value\MediaType\MediaType;

/**
 * @coversDefaultClass \Stadly\FileWaiter\Adapter\Flysystem
 * @covers ::<protected>
 * @covers ::<private>
 */
final class FlysystemTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructFlysystemAdapter(): void
    {
        $filePathLocal = __FILE__;
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());

        // Force generation of code coverage
        $fileConstruct = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        self::assertEquals($file, $fileConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFlysystemAdapterToNonExistingFile(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);
        unlink($filePathLocal);

        $this->expectException(FileCouldNotBeFound::class);
        new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFlysystemAdapterToDirectory(): void
    {
        $filePathLocal = __DIR__;
        $filePath = basename($filePathLocal);

        $this->expectException(FileCouldNotBeFound::class);
        new Flysystem(new Filesystem(new Local(dirname(__DIR__))), $filePath, new HttpFactory());
    }

    /**
     * @covers ::getFileStream
     */
    public function testFileStreamEmitsFileContents(): void
    {
        $filePathLocal = __FILE__;
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());

        self::assertStringEqualsFile($filePathLocal, (string)$file->getFileStream());
    }

    /**
     * @covers ::getFileStream
     */
    public function testCannotOpenFileStreamToNonExistingFile(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);

        $this->expectException(StreamCouldNotBeOpened::class);
        $file->getFileStream();
    }

    /**
     * @covers ::getFileName
     */
    public function testCanGetFileName(): void
    {
        $filePathLocal = __FILE__;
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());

        self::assertSame($filePath, $file->getFileName());
    }

    /**
     * @covers ::getFileName
     */
    public function testCanGetFileNameOfNonExistingFile(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);

        self::assertSame($filePath, $file->getFileName());
    }

    /**
     * @covers ::getFileName
     */
    public function testCanGetFileNameOfDirectory(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);
        mkdir($filePathLocal);

        self::assertSame($filePath, $file->getFileName());

        rmdir($filePathLocal);
    }

    /**
     * @covers ::getFileSize
     */
    public function testCanGetFileSize(): void
    {
        $filePathLocal = __FILE__;
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());

        self::assertSame(filesize($filePathLocal), $file->getFileSize());
    }

    /**
     * @covers ::getFileSize
     */
    public function testFileSizeOfNonExistingFileIsNull(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);

        self::assertNull($file->getFileSize());
    }

    /**
     * @covers ::getFileSize
     */
    public function testFileSizeOfDirectoryIsNull(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);
        mkdir($filePathLocal);

        self::assertNull($file->getFileSize());

        rmdir($filePathLocal);
    }

    /**
     * @covers ::getMediaType
     */
    public function testCanGetMediaType(): void
    {
        $filePathLocal = __FILE__;
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());

        $mimeType = mime_content_type($filePathLocal);
        assert($mimeType !== false);

        self::assertEquals(MediaType::fromString($mimeType), $file->getMediaType());
    }

    /**
     * @covers ::getMediaType
     */
    public function testMediaTypeOfNonExistingFileIsNull(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);

        self::assertNull($file->getMediaType());
    }

    /**
     * @covers ::getMediaType
     */
    public function testMediaTypeOfDirectoryIsNull(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);
        mkdir($filePathLocal);

        self::assertNull($file->getMediaType());

        rmdir($filePathLocal);
    }

    /**
     * @covers ::getLastModifiedDate
     */
    public function testCanGetLastModifiedDate(): void
    {
        $filePathLocal = __FILE__;
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());

        $timestamp = filemtime($filePathLocal);
        assert($timestamp !== false);

        self::assertEquals(Date::fromTimestamp($timestamp), $file->getLastModifiedDate());
    }

    /**
     * @covers ::getLastModifiedDate
     */
    public function testLastModifiedDateOfNonExistingFileIsNull(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);

        self::assertNull($file->getLastModifiedDate());
    }

    /**
     * @covers ::getLastModifiedDate
     */
    public function testCanGetLastModifiedDateOfDirectory(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);
        mkdir($filePathLocal);

        $timestamp = filemtime($filePathLocal);
        assert($timestamp !== false);

        self::assertEquals(Date::fromTimestamp($timestamp), $file->getLastModifiedDate());

        rmdir($filePathLocal);
    }

    /**
     * @covers ::getEntityTag
     */
    public function testCanGetEntityTag(): void
    {
        $filePathLocal = __FILE__;
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());

        $md5 = md5_file($filePathLocal);
        assert($md5 !== false);

        self::assertEquals(new EntityTag($md5), $file->getEntityTag());
    }

    /**
     * @covers ::getEntityTag
     */
    public function testEntityTagOfNonExistingFileIsNull(): void
    {
        $filePathLocal = tempnam(__DIR__, 'tmp');
        assert($filePathLocal !== false);
        $filePath = basename($filePathLocal);

        $file = new Flysystem(new Filesystem(new Local(__DIR__)), $filePath, new HttpFactory());
        unlink($filePathLocal);

        self::assertNull($file->getEntityTag());
    }
}
