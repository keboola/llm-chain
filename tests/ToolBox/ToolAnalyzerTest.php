<?php

declare(strict_types=1);

namespace SymfonyLlm\LlmChain\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SymfonyLlm\LlmChain\Exception\InvalidToolImplementation;
use SymfonyLlm\LlmChain\Tests\ToolBox\Tool\ToolMultiple;
use SymfonyLlm\LlmChain\Tests\ToolBox\Tool\ToolRequiredParams;
use SymfonyLlm\LlmChain\Tests\ToolBox\Tool\ToolWrong;
use SymfonyLlm\LlmChain\ToolBox\Metadata;
use SymfonyLlm\LlmChain\ToolBox\ParameterAnalyzer;
use SymfonyLlm\LlmChain\ToolBox\ToolAnalyzer;

#[CoversClass(ToolAnalyzer::class)]
final class ToolAnalyzerTest extends TestCase
{
    private ToolAnalyzer $toolAnalyzer;

    protected function setUp(): void
    {
        $this->toolAnalyzer = new ToolAnalyzer(new ParameterAnalyzer());
    }

    public function testWithoutAttribute(): void
    {
        $this->expectException(InvalidToolImplementation::class);
        iterator_to_array($this->toolAnalyzer->getMetadata(ToolWrong::class));
    }

    public function testGetDefinition(): void
    {
        /** @var Metadata[] $actual */
        $actual = iterator_to_array($this->toolAnalyzer->getMetadata(ToolRequiredParams::class));

        self::assertCount(1, $actual);
        self::assertSame(ToolRequiredParams::class, $actual[0]->className);
        self::assertSame('tool_required_params', $actual[0]->name);
        self::assertSame('A tool with required parameters', $actual[0]->description);
        self::assertSame('bar', $actual[0]->method);
        self::assertIsArray($actual[0]->parameters);
    }

    public function testGetDefinitionWithMultiple(): void
    {
        $actual = iterator_to_array($this->toolAnalyzer->getMetadata(ToolMultiple::class));

        self::assertCount(2, $actual);

        self::assertSame(ToolMultiple::class, $actual[0]->className);
        self::assertSame('tool_hello_world', $actual[0]->name);
        self::assertSame('Function to say hello', $actual[0]->description);
        self::assertSame('hello', $actual[0]->method);
        self::assertIsArray($actual[0]->parameters);

        self::assertSame(ToolMultiple::class, $actual[1]->className);
        self::assertSame('tool_required_params', $actual[1]->name);
        self::assertSame('Function to say a number', $actual[1]->description);
        self::assertSame('bar', $actual[1]->method);
        self::assertIsArray($actual[1]->parameters);
    }
}
