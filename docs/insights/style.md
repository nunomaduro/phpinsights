# Style

All insights about style are regrouped in one Metric:

* `NunoMaduro\PhpInsights\Domain\Metrics\Style\Style` <Badge text="Style" type="warn" vertical="middle"/>

## Closing tag <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that the file does not end with a closing tag.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\ClosingTagSniff`

## End file newline <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures the file ends with a newline character.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR2\Sniffs\Files\EndFileNewlineSniff`

## Side effects <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures a file declares new symbols and causes no other side effects, or executes logic with side effects, but not both.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff`

## Git merge conflict <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks for merge conflict artifacts.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\VersionControl\GitMergeConflictSniff`

##  <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff detects BOMs that may corrupt application work.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff`

## Line endings <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that end of line characters are correct.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff`

<details>
    <summary>Configuration</summary>

```php
    \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff::class => [
        'eolChar' => '\n',
    ]
```
</details>

## Function closing brace <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that the closing brace of a function goes directly after the body.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\FunctionClosingBraceSniff`

## Object operator indent <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that object operators are indented correctly.

**Insight Class**: `PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ObjectOperatorIndentSniff`

<details>
    <summary>Configuration</summary>

```php
    \PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ObjectOperatorIndentSniff::class => [
        'indent' => 4,
    ]
```
</details>

## Scope closing brace <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that the closing braces of scopes are aligned correctly.

**Insight Class**: `PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff`

<details>
    <summary>Configuration</summary>

```php
    \PHP_CodeSniffer\Standards\PEAR\Sniffs\WhiteSpace\ScopeClosingBraceSniff::class => [
        'indent' => 4,
    ]
```
</details>

## Disallow long array syntax <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff bans the use of the PHP long array syntax (`array()`).
Use `[]` instead.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\DisallowLongArraySyntaxSniff`

## Line length <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks the length of all lines in a file.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff`

<details>
    <summary>Configuration</summary>

```php
    \PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class => [
        'lineLimit' => 80,
        'absoluteLineLimit' => 100,
        'ignoreComments' => false,
    ]
```
</details>

## Space after cast <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures there is a single space after cast tokens.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterCastSniff`

## Space after not <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures there is a single space after a NOT operator.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff`

## Function call argument spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that calls to methods and functions are spaced correctly.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\FunctionCallArgumentSpacingSniff`

## Character before PHP opening tag <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that the opening PHP tag is the first content in a file.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\CharacterBeforePHPOpeningTagSniff`

## Backtick Operator <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff disallows the use of the backtick execution operator.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\BacktickOperatorSniff`

## Disallow alternative PHP tags <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff verifies that no alternative PHP tags are used.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowAlternativePHPTagsSniff`

## Disallow short open tag <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff makes sure that shorthand PHP open tags are not used.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff`

## Forbidden functions <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff discourages the use of alias functions.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff`

## Lower case constant <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that all uses of `true`, `false` and `null` are lowercase.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff`

## Lower case keyword <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that all PHP keywords are lowercase.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseKeywordSniff`

## Lower case type <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks that all PHP types are lowercase.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseTypeSniff`

## SAPI Usage <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures the `PHP_SAPI` constant is used instead of `php_sapi_name()`.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\SAPIUsageSniff`

## Syntax <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures PHP believes the syntax is clean.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\SyntaxSniff`

## Trailing array comma <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff enforces trailing commas in multi-line arrays and requires short array syntax `[]`.
Commas after last element in an array make adding a new element easier and result in a cleaner versioning diff.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff`

## Arbitraty parentheses spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks whitespace on the inside of arbitrary parentheses.

Arbitrary parentheses are those which are not owned by a function (call), array or control structure.
Spacing on the outside is not checked on purpose as this would too easily conflict with other spacing rules.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ArbitraryParenthesesSpacingSniff`

## Disallow tab indent <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff throws errors if tabs are used for indentation.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff`

## Increment decrement spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff verifies spacing between variables and increment/decrement operators.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\IncrementDecrementSpacingSniff`

## Language construct spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures all language constructs contain a single space between themselves and their content.

**Insight Class**: `PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff`

## Camel caps method name <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures method names are defined using camel case.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff`

## Else If declaration <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff verifies that there are no else if statements (elseif should be used instead).

**Insight Class**: `PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\ElseIfDeclarationSniff`

## Switch declaration <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures all switch statements are defined correctly.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff`

<details>
    <summary>Configuration</summary>

```php
    \PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff::class => [
        'indent' => 4,
    ]
```
</details>

## Upper case constant name <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff ensures that constant names are all uppercase.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff`

## Alphabetically sorted uses <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks whether uses at the top of a file are alphabetically sorted.
Follows natural sorting and takes edge cases with special symbols into consideration.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff`

## Namespace Spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff enforces configurable number of lines before and after `namespace`.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\NamespaceSpacingSniff`

<details>
    <summary>Configuration</summary>

```php
    \SlevomatCodingStandard\Sniffs\Namespaces\NamespaceSpacingSniff::class => [
        'linesCountBeforeNamespace' => 1,
        'linesCountAfterNamespace' => 1,
    ]
```
</details>

## Require one namespace in file <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks there is only one namespace in a file.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\RequireOneNamespaceInFileSniff`

## Unused uses <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff detects unused `use` in a file.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff`

<details>
    <summary>Configuration</summary>

```php
    \SlevomatCodingStandard\Sniffs\Namespaces\UnusedUsesSniff::class => [
        'searchAnnotations' => false,
        'ignoredAnnotationNames' => [], // case sensitive list of annotation names that the sniff should ignore (only the name is ignored, annotation content is still searched). Useful for name collisions like @testCase annotation and TestCase class.
        'ignoredAnnotations' => [], // case sensitive list of annotation names that the sniff ignore completely (both name and content are ignored). Useful for name collisions like @group Cache annotation and Cache class
    ]
```
</details>

## Use does not start with Backslash <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff disallows leading backslash in use statement.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\UseDoesNotStartWithBackslashSniff`

## Use spacing sniff <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff enforces configurable number of lines before first use, after last use and between two use statements.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\UseSpacingSniff`

<details>
    <summary>Configuration</summary>

```php
    \SlevomatCodingStandard\Sniffs\Namespaces\UseSpacingSniff::class => [
        'linesCountBeforeFirstUse' => 1,
        'linesCountBetweenUseTypes' => 0,
        'linesCountAfterLastUse' => 1,
    ]
```
</details>

## Spread operator spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff enforces configurable number of spaces after the `...` operator.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Operators\SpreadOperatorSpacingSniff`

<details>
    <summary>Configuration</summary>

```php
    \SlevomatCodingStandard\Sniffs\Operators\SpreadOperatorSpacingSniff::class => [
        'spacesCountAfterOperator' => 0,
    ]
```
</details>

## Short list <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff enforces using short form of list syntax, `[...]` instead of `list(...)`.

**Insight Class**: `SlevomatCodingStandard\Sniffs\PHP\ShortListSniff`

## Parameter type hint spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff
- checks that there's a single space between a typehint and a parameter name: `Foo $foo`
- checks that there's no whitespace between a nullability symbol and a typehint: `?Foo`

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSpacingSniff`

## Return type hint spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff enforces consistent formatting of return typehints.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff`

<details>
    <summary>Configuration</summary>

```php
    \SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff::class => [
        'spacesCountBeforeColon' => 0,
    ]
```
</details>

## Superfluous Whitespace <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff checks for unneeded whitespace.

**Insight Class**: `PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff`

<details>
    <summary>Configuration</summary>

```php
    \PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff::class => [
        'ignoreBlankLines' => false,
    ]
```
</details>

## Doc comment spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff enforces configurable number of lines before first content (description or annotation), after last content (description or annotation), between description and annotations, between two different annotations types (eg. between `@param` and `@return`).

**Insight Class**: `SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff`

<details>
    <summary>Configuration</summary>

```php
    \vomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff::class => [
        'linesCountBeforeFirstContent' => 0,
        'linesCountBetweenDescriptionAndAnnotations' => 1,
        'linesCountBetweenDifferentAnnotationsTypes' => 0,
        'linesCountBetweenAnnotationsGroups' => 1,
        'linesCountAfterLastContent' => 0,
        'annotationsGroups' => [],
    ]
```
</details>

## Class instantiation <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff verifies that classes are instantiated with parentheses.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR12\Sniffs\Classes\ClassInstantiationSniff`

<!--
Insight template
##  <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff

**Insight Class**: ``

<details>
    <summary>Configuration</summary>

```php

```
</details>
-->