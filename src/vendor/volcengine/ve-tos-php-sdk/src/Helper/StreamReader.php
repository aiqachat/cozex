<?php

namespace Tos\Helper;

use Psr\Http\Message\StreamInterface;

if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
    class StreamReader implements StreamInterface
    {
        /**
         * @var StreamInterface
         */
        private $origin;

        /**
         * @var int
         */
        private $contentLength;

        /**
         * @var int
         */
        private $remainSize;

        /**
         * @var string
         */
        private $result;
        /**
         * @var bool
         */
        private $calcCrc64;

        const DefaultChunkSize = 65536;

        public function __construct(StreamInterface $origin = null, $contentLength = 0, $calcCrc64 = false)
        {
            $this->origin = $origin;
            if ($contentLength < 0) {
                $contentLength = 0;
            }
            $this->contentLength = intval($contentLength);
            $this->remainSize = $this->contentLength;
            $this->calcCrc64 = $calcCrc64;
        }

        public function __toString(): string
        {
            return $this->getContents();
        }

        public function close(): void
        {
            if ($this->origin) {
                $this->origin->close();
            }
        }

        public function detach()
        {
            if ($this->origin) {
                return $this->origin->detach();
            }
            return null;
        }

        public function getSize(): ?int
        {
            return $this->contentLength;
        }

        public function tell(): int
        {
            if ($this->origin) {
                $this->origin->tell();
            }
            return 0;
        }

        public function eof(): bool
        {
            return $this->remainSize <= 0;
        }

        public function isSeekable(): bool
        {
            return false;
        }

        public function seek($offset, $whence = SEEK_SET): void
        {
            throw new \RuntimeException('Stream is not seekable');
        }

        public function rewind(): void
        {
            throw new \RuntimeException('Stream is not seekable');
        }

        public function isWritable(): bool
        {
            return false;
        }

        public function write($string): int
        {
            throw new \RuntimeException('Stream is not writable');
        }

        public function isReadable(): bool
        {
            return true;
        }

        public function read($length): string
        {
            if ($this->eof()) {
                return '';
            }

            if (!$length || $length <= 0) {
                $length = self::DefaultChunkSize;
            }

            $chunkSizeOnce = $this->remainSize >= $length ? $length : $this->remainSize;
            $chunk = $this->origin->read($chunkSizeOnce);
            $this->remainSize -= strlen($chunk);
            return $chunk;
        }

        public function getContents(): string
        {
            if (isset($this->result)) {
                return $this->result;
            }
            $result = '';
            while (!$this->eof()) {
                $chunk = $this->read(self::DefaultChunkSize);
                $result .= $chunk;
            }
            $this->result = $result;
            return $result;
        }

        public function getMetadata($key = null)
        {
            return null;
        }
    }
} else {
    class StreamReader implements StreamInterface
    {
        /**
         * @var StreamInterface
         */
        private $origin;

        /**
         * @var int
         */
        private $contentLength;

        /**
         * @var int
         */
        private $remainSize;

        /**
         * @var string
         */
        private $result;
        /**
         * @var bool
         */
        private $calcCrc64;

        const DefaultChunkSize = 65536;

        public function __construct(StreamInterface $origin = null, $contentLength = 0, $calcCrc64 = false)
        {
            $this->origin = $origin;
            if ($contentLength < 0) {
                $contentLength = 0;
            }
            $this->contentLength = intval($contentLength);
            $this->remainSize = $this->contentLength;
            $this->calcCrc64 = $calcCrc64;
        }

        public function __toString()
        {
            return $this->getContents();
        }

        public function close()
        {
            if ($this->origin) {
                $this->origin->close();
            }
        }

        public function detach()
        {
            if ($this->origin) {
                return $this->origin->detach();
            }
            return null;
        }

        public function getSize()
        {
            return $this->contentLength;
        }

        public function tell()
        {
            if ($this->origin) {
                $this->origin->tell();
            }
            return 0;
        }

        public function eof()
        {
            return $this->remainSize <= 0;
        }

        public function isSeekable()
        {
            return false;
        }

        public function seek($offset, $whence = SEEK_SET)
        {
            throw new \RuntimeException('Stream is not seekable');
        }

        public function rewind()
        {
            throw new \RuntimeException('Stream is not seekable');
        }

        public function isWritable()
        {
            return false;
        }

        public function write($string)
        {
            throw new \RuntimeException('Stream is not writable');
        }

        public function isReadable()
        {
            return true;
        }

        public function read($length)
        {
            if ($this->eof()) {
                return '';
            }

            if (!$length || $length <= 0) {
                $length = self::DefaultChunkSize;
            }

            $chunkSizeOnce = $this->remainSize >= $length ? $length : $this->remainSize;
            $chunk = $this->origin->read($chunkSizeOnce);
            $this->remainSize -= strlen($chunk);
            return $chunk;
        }

        public function getContents()
        {
            if (isset($this->result)) {
                return $this->result;
            }
            $result = '';
            while (!$this->eof()) {
                $chunk = $this->read(self::DefaultChunkSize);
                $result .= $chunk;
            }
            $this->result = $result;
            return $result;
        }

        public function getMetadata($key = null)
        {
            return null;
        }
    }
}
