<?php

namespace Jstewmc\Chunker;

class TextTest extends TestCase
{
    /**
     * Use a chunk size of one character for string tests.
     */
    private const SIZE = 1;

    public function testConstructThrowsInvalidArgumentExceptionWhenEncodingIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $chunker = new Text('foo', 'foo');
    }

    public function testConstructThrowsInvalidArgumentExceptionWhenSizeIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $chunker = new Text('foo', null, -1);
    }

    public function testConstructThrowsInvalidArgumentExceptionWhenSizeIsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $chunker = new Text('foo', null, 0);
    }

    public function testGetCurrentChunkReturnsFalseWhenChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->getCurrentChunk());
    }

    public function testGetCurrentChunkReturnsStringWhenTextIsSingleByteString(): void
    {
        $this->assertEquals(
            $this->singleByteChunk1(),
            $this->singleByteChunker()->getCurrentChunk()
        );
    }

    public function testGetCurrentChunkReturnsStringWhenTextIsMultiByteString(): void
    {
        $this->assertEquals(
            $this->multiByteChunk1(),
            $this->multiByteChunker()->getCurrentChunk()
        );
    }

    public function testCountChunksReturnsIntWhenTextIsEmpty(): void
    {
        $this->assertEquals(0, $this->emptyChunker()->countChunks());
    }

    public function testCountChunksReturnsIntWhenTextIsSingleByteString(): void
    {
        $this->assertEquals(3, $this->singleByteChunker()->countChunks());
    }

    public function testCountChunksReturnsIntWhenTextIsMultiByteString(): void
    {
        $this->assertEquals(3, $this->multiByteChunker()->countChunks());
    }

    public function testGetNextChunkReturnsFalseWhenNextChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->getNextChunk());
    }

    public function testGetNextChunkReturnsStringWhenNextChunkIsSingleByteString(): void
    {
        $this->assertEquals(
            $this->singleByteChunk2(),
            $this->singleByteChunker()->getNextChunk()
        );
    }

    public function testGetNextChunkReturnsStringWhenNextChunkIsMultiByteString(): void
    {
        $this->assertEquals(
            $this->multiByteChunk2(),
            $this->multiByteChunker()->getNextChunk()
        );
    }

    public function testGetPreviousChunkReturnsFalseWhenPreviousChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->getPreviousChunk());
    }

    public function testGetPreviousChunkReturnsFalseWhenPreviousChunkIsSingleByteString(): void
    {
        $chunker = $this->singleByteChunker();

        $chunker->next();

        $this->assertEquals($this->singleByteChunk1(), $chunker->getPreviousChunk());
    }

    public function testGetPreviousChunkReturnsFalseWhenPreviousChunkIsMultiByteString(): void
    {
        $chunker = $this->multiByteChunker();

        $chunker->next();

        $this->assertEquals($this->multiByteChunk1(), $chunker->getPreviousChunk());
    }

    public function testHasChunkReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse($this->emptyChunker()->hasChunk());
    }

    public function testHasChunkReturnsTrueWhenTextHasOneChunk(): void
    {
        $this->assertTrue($this->oneChunkChunker()->hasChunk());
    }

    public function testHasChunkReturnsFalseWhenTextHasManyChunks(): void
    {
        $this->assertFalse($this->manyChunkChunker()->hasChunk());
    }

    public function testHasChunksReturnsFalseWhenTextIsEmpty(): void
    {
        $this->assertFalse($this->emptyChunker()->hasChunks());
    }

    public function testHasChunksReturnsTrueWhenTextHasOneChunk(): void
    {
        $this->assertTrue($this->oneChunkChunker()->hasChunks());
    }

    public function testHasChunksReturnsFalseWhenTextHasManyChunks(): void
    {
        $this->assertTrue($this->manyChunkChunker()->hasChunks());
    }

    public function testHasNextChunkReturnsFalseWhenNextChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->hasNextChunk());
    }

    public function testHasNextChunkReturnsTrueWhenNextChunkExists(): void
    {
        $this->assertTrue($this->singleByteChunker()->hasNextChunk());
    }

    public function testHasPreviousChunkReturnsFalseWhenPreviousChunkDoesNotExist(): void
    {
        $this->assertFalse($this->emptyChunker()->hasPreviousChunk());
    }

    public function testHasPreviousChunkReturnsTrueWhenPreviousChunkExists(): void
    {
        $chunker = $this->singleByteChunker();

        $chunker->next();

        $this->assertTrue($chunker->hasPreviousChunk());
    }

    public function testResetResetsInternalPointer(): void
    {
        $chunker = $this->singleByteChunker();

        $chunker->next();

        $this->assertEquals(1, $chunker->getIndex());

        $chunker->reset();

        $this->assertEquals(0, $chunker->getIndex());
    }

    protected function emptyChunker(): Text
    {
        return new Text('');
    }

    protected function singleByteChunker(): Text
    {
        return new Text($this->singleByteString(), self::ENCODING, self::SIZE);
    }

    protected function multiByteChunker(): Text
    {
        return new Text($this->multiByteString(), self::ENCODING, self::SIZE);
    }

    protected function oneChunkChunker(): Text
    {
        return new Text('a', self::ENCODING, self::SIZE);
    }

    protected function singleByteString(): string
    {
        return 'foo';
    }

    protected function singleByteChunk1(): string
    {
        return 'f';
    }

    protected function singleByteChunk2(): string
    {
        return 'o';
    }

    protected function singleByteChunk3(): string
    {
        return 'o';
    }

    protected function multiByteString(): string
    {
        return "{$this->twoByteCharacter()} {$this->threeByteCharacter()}";
    }

    protected function multiByteChunk1(): string
    {
        return $this->twoByteCharacter();
    }

    protected function multiByteChunk2(): string
    {
        return ' ';
    }

    protected function multiByteChunk3(): string
    {
        return $this->threeByteCharacter();
    }
}
