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

## Byte order mark <Badge text="^1.0"/> <Badge text="Style" type="warn"/>

This sniff detects BOMs that may corrupt application work.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff`

## Line endings <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Object operator indent <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Scope closing brace <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Line length <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Switch declaration <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Namespace Spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Unused uses <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Use spacing sniff <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Spread operator spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Return type hint spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Superfluous Whitespace <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

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

## Doc comment spacing <Badge text="^1.0"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

This sniff enforces configurable number of lines before first content (description or annotation), after last content (description or annotation), between description and annotations, between two different annotations types (eg. between `@param` and `@return`).

**Insight Class**: `SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff`

<details>
    <summary>Configuration</summary>

```php
\SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff::class => [
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

## No trailing comma in singleline array <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

This fixer remove trailing commas in list function calls.

**Insight Class**: `PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer`

## No whitespace before comma in array <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

In array declaration, there MUST NOT be a whitespace before each comma.

**Insight Class**: `PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer::class => [
    'after_heredoc' => false, // Whether the whitespace between heredoc end and comma should be removed.
]
```
</details>

## Braces <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

The body of each structure must be enclosed by braces.
Braces should be properly placed.
Body of braces should be properly indented.

**Insight Class**: `PhpCsFixer\Fixer\Basic\BracesFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\Basic\BracesFixer::class => [
    'allow_single_line_closure' => false,
    'position_after_anonymous_constructs' => 'same', // possible values ['same', 'next']
    'position_after_control_structures' => 'same', // possible values ['same', 'next']
    'position_after_functions_and_oop_constructs' => 'same', // possible values ['same', 'next']
]
```
</details>

## Encoding <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

PHP code must use only UTF-8 without BOM (remove BOM).

**Insight Class**: `PhpCsFixer\Fixer\Basic\EncodingFixer`

## Lowercase static reference <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

Class static references `self`, `static` and `parent` must be in lower case.

**Insight Class**: `PhpCsFixer\Fixer\Casing\LowercaseStaticReferenceFixer`

## Magic constant casing <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

Magic constants should be referred to using the correct casing.

**Insight Class**: `PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer`

## Magic method casing <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

Magic method definitions and calls must be using the correct casing.

**Insight Class**: `PhpCsFixer\Fixer\Casing\MagicMethodCasingFixer`

## Native function casing <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

Function defined by PHP should be called using the correct casing.

**Insight Class**: `PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer`

## Native function type declaration casing <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

Native type hints for functions should use the correct case.

**Insight Class**: `PhpCsFixer\Fixer\Casing\NativeFunctionTypeDeclarationCasingFixer`

## Cast spaces <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

A single space or none should be between cast and variable.

**Insight Class**: `PhpCsFixer\Fixer\CastNotation\CastSpacesFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\CastNotation\CastSpacesFixer::class => [
    'space' => 'single' // possible values ['single', 'none']
]
```
</details>

## Class definition fixer <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

Whitespace around the keywords of a class, trait or interfaces definition should be one space.

**Insight Class**: `PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer::class => [
    'multi_line_extends_each_single_line' => false,
    'single_item_single_line' => false,
    'single_line' => false,
]
```
</details>

## No blank lines after class opening <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

Ensure there is no code on the same line as the PHP open tag and it is followed by a blank line.

**Insight Class**: `PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer`

## No trailing whitespace in comment <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

There must be no trailing spaces inside comment or PHPDoc.

**Insight Class**: `PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer`

## Switch case semicolon to colon <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

A case should be followed by a colon and not a semicolon.

**Insight Class**: `PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer`

## Switch case space <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

This fixer removes extra spaces between colon and case value.

**Insight Class**: `PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer`

## Function declaration <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

Spaces should be properly placed in a function declaration.

**Insight Class**: `PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer::class => [
    'closure_function_spacing' => 'one' // possible values ['one', 'none']
]
```
</details>

## Function typehint space <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

This fixer add missing space between function's argument and its typehint.

**Insight Class**: `PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer`

## Binary operator space <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

Binary operators should be surrounded by space as configured.

**Insight Class**: `PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer::class => [
    'align_double_arrow' => false, // Whether to apply, remove or ignore double arrows alignment: possibles values [true, false, null]
    'align_equals' => false, // Whether to apply, remove or ignore equals alignment: possibles values [true, false, null]
    'default' => 'single_space', // default fix strategie: possibles values ['align', 'align_single_space', 'align_single_space_minimal', 'single_space', 'no_space', null]
]
```
</details>

## Standardize not equals <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

This fixer replace all `<>` with `!=`.

**Insight Class**: `PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer`

## Align multiline comment <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

Each line of multi-line DocComments must have an asterisk and must be aligned with the first one.

**Insight Class**: `PhpCsFixer\Fixer\Phpdoc\AlignMultilineCommentFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\Phpdoc\AlignMultilineCommentFixer::class => [
    'comment_type' => 'phpdocs_only' // possible values ['phpdocs_only', 'phpdocs_like', 'all_multiline']
]
```
</details>

## Full opening tag <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

PHP code must use the long `<?php` tags or short-echo `<?=` tags and not other tag variations.

**Insight Class**: `PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer`

## No singleline whitespace before semicolons <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

Single-line whitespace before closing semicolon are prohibited.

**Insight Class**: `PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer`

## Single quote <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

This fixer converts double quotes to single quotes for simple strings.

**Insight Class**: `PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer::class => [
    'strings_containing_single_quote_chars' => false,
]
```
</details>

## Method chaining indentation <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

Method chaining must be properly indented.
Method chaining with different levels of indentation is not supported.

**Insight Class**: `PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer`

## No extra blank lines <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

This fixer removes extra blank lines and/or blank lines following configuration.

**Insight Class**: `PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer::class => [
    'tokens' => ['extra'], // possibles values ['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait']
]
```
</details>

## No spaces around offset <Badge text="^1.8"/> <Badge text="Style" type="warn"/> <Badge text="configurable"/>

There must not be spaces around offset braces.

**Insight Class**: `PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer::class => [
    'positions' => ['inside', 'outside'],
]
```
</details>

## No spaces inside parenthesis <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

There must not be a space after the opening parenthesis.
There must not be a space before the closing parenthesis.

**Insight Class**: `PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer`

## No trailing whitespace <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

This fixer removes trailing whitespace at the end of non-blank lines.

**Insight Class**: `PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer`

## No whitespace in blank line <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

This fixer removes trailing whitespace at the end of blank lines.

**Insight Class**: `PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer`

## Single blank line at eof <Badge text="^1.8"/> <Badge text="Style" type="warn"/>

A PHP file without end tag must always end with a single empty line feed.

**Insight Class**: `PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer`

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
