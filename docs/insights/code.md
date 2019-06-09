# Code Insights

The following insights are in organised in differents metrics :

* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Classes` <Badge text="Code\Classes" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Code` <Badge text="Code\Code" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments` <Badge text="Code\Comments" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Functions` <Badge text="Code\Functions" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Globally` <Badge text="Code\Globally" type="warn" vertical="middle"/>


## Forbidden public property <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff disallow public properties.

**Insight Class**: `ObjectCalisthenics\Sniffs\Classes\ForbiddenPublicPropertySniff`

## Unused private elements <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff detect unused private elements

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\UnusedPrivateElementsSniff`

## Forbidden setter <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff disallows setter methods.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff`

## Unnecessary Final modifier  <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff detects unnecessary final modifiers inside of final classes.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UnnecessaryFinalModifierSniff`

## Property declaration  <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff verifies that properties are declared correctly.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff`

## Class constant visibilty <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff requires declaring visibility for all class constants.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff`

## Disallow Late static Bindig for constants <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff disallows late static binding for constants.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\DisallowLateStaticBindingForConstantsSniff`

## Modern ClassName reference  <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff reports use of \_\_CLASS\_\_, get_parent_class(), get_called_class(), get_class() and get_class($this). Class names should be referenced via ::class constant when possible.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff`

## Useless Late Static Binding <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff reports useless late static binding.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\UselessLateStaticBindingSniff`

## Unused variable  <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff detects unused variables.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff`

## Code Analyzer  <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff runs the Zend Code Analyzer (from Zend Studio) on files.

**Insight Class**: `PHP_CodeSniffer\Standards\Zend\Sniffs\Debug\CodeAnalyzerSniff`

## Switch declaration <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff ensures all switch statements are defined correctly.

**Insight Class**: `PHP_CodeSniffer\Standards\PSR2\Sniffs\ControlStructures\SwitchDeclarationSniff`

## Language Construct spacing <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff ensures all language constructs contain a single space between themselves and their content

**Insight Class**: `PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\LanguageConstructSpacingSniff`

## Element name minimal length <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

**Insight Class**: `ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff`

<details>
    <summary>Configuration</summary>

```php
        \ObjectCalisthenics\Sniffs\NamingConventions\ElementNameMinimalLengthSniff::class => [
            'minLength' => 3,
            'allowedShortNames' => ['i', 'id', 'to', 'up'],
        ]
```
</details>

## Max nesting level <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

**Insight Class**: `ObjectCalisthenics\Sniffs\Metrics\MaxNestingLevelSniff`

<details>
    <summary>Configuration</summary>

```php
        \ObjectCalisthenics\Sniffs\Metrics\MaxNestingLevelSniff::class => [
            'maxNestingLevel' => 2,
        ]
```
</details>

## Useless Variable <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

**Insight Class**: `SlevomatCodingStandard\Sniffs\Variables\UselessVariableSniff`

## Eval <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff detects usage of `eval()` function.

**Insight Class**: `PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\EvalSniff`

## Array indent <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff ensure array are correctly indented

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff`

<details>
    <summary>Configuration</summary>

```php
        \PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays\ArrayIndentSniff::class => [
            'indent' => 4,
        ]
```
</details>

## Empty PHP statement  <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

Checks against empty PHP statements.

- Check against two semi-colons with no executable code in between.
- Check against an empty PHP open - close tag combination.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyPHPStatementSniff`

## Empty Statement <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff detects empty statement.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\EmptyStatementSniff`


## For loop should be While loop <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff detects for-loops that can be simplified to a while-loop

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\ForLoopShouldBeWhileLoopSniff`

## For loop with test function call  <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff detects for-loops that use a function call in the test expression.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\ForLoopWithTestFunctionCallSniff`

## Jumbled Incrementer <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff detects the usage of one and the same incrementer into an outer and an inner loop.
Even it is intended this is confusing code.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\JumbledIncrementerSniff`

## Unconditional If statement <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff detects statement conditions that are only set to one of the constant values <b>true</b> or <b>false</b>

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UnconditionalIfStatementSniff`

## Useless Overriding Method <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff  detects the use of methods that only call their parent classes's method with the same name and arguments.
These methods are not required.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UselessOverridingMethodSniff`

## Inline control structure  <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff verifies that inline control statements are not present.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\ControlStructures\InlineControlStructureSniff`

## Disallow multiple statements <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff ensures each statement is on a line by itself.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\DisallowMultipleStatementsSniff`

## Backtick Operator <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows the use of the backtick execution operator.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\BacktickOperatorSniff`

## Discourage GOTO <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff discourage the use of the PHP `goto` language construct

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DiscourageGotoSniff`

## No silenced errors <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff detects when any code prefixed with an asperand is encountered.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\NoSilencedErrorsSniff`

## Unnecessary string concat  <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff checks that two strings are not concatenated together; suggests using one string instead.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Strings\UnnecessaryStringConcatSniff`

## Short form type keywords <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff verifies that the short form of type keywords is used (e.g., int, bool).

**Insight Class**: `PHP_CodeSniffer\Standards\PSR12\Sniffs\Keywords\ShortFormTypeKeywordsSniff`

## Disallow implicit array creation <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows implicit array creation.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Arrays\DisallowImplicitArrayCreationSniff`

## Assignment in condition <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows assignment in conditions

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff`

## Disallow continue without integer operand in switch <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows use of `continue` without integer operand in switch because it emits a warning in PHP 7.3 and higher.

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\DisallowContinueWithoutIntegerOperandInSwitchSniff`

## Disallow empty <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows use of `empty()`.

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff`

## Disallow short ternary operator <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows short ternary operator `?:`.

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\DisallowShortTernaryOperatorSniff`

## Disallow Yoda Comparison <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

Yoda conditions decrease code comprehensibility and readability by switching operands around comparison operators forcing the reader to read the code in an unnatural way.

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff`

## Require Yoda Comparison <Badge text="^1.0"/> <Badge text="not enabled" type="error"/>

This sniff enforces yoda comparison usage

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\RequireYodaComparisonSniff`


## Language Construct With Parentheses <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\LanguageConstructWithParenthesesSniff`

## Dead catch <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff finds unreachable catch blocks.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Exceptions\DeadCatchSniff`

<details>
    <summary>Example</summary>

```php
try {
    doStuff();
} catch (\Throwable $e) {
    log($e);
} catch (\InvalidArgumentException $e) {
    // unreachable!
}
```
</details>

## Unused Inherited variable passed to closure <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/> <Badge text="Code\Functions" type="warn"/>

This sniff looks for unused inherited variables passed to closure via `use`.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff`

## Unused parameter <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff looks for unused parameters.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff`

## Useless Parameter default value <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff looks for useless parameter default value.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Functions\UselessParameterDefaultValueSniff`

## Use from same namespace <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff prohibits uses from the same namespace.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\UseFromSameNamespaceSniff`

## Useless Alias <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff looks for use alias that is same as unqualified name.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\UselessAliasSniff`

## Disallow equal operators <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows using loose `==` and `!=` comparison operators.
Use `===` and `!==` instead, they are much more secure and predictable.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Operators\DisallowEqualOperatorsSniff`

## Require combined assignment operator <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff requires using combined assignment operators, eg `+=`, `.=` etc.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Operators\RequireCombinedAssignmentOperatorSniff`

## Require only standalone increment and decrement operators <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff reports `++` and `--` operators not used standalone.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Operators\RequireOnlyStandaloneIncrementAndDecrementOperatorsSniff`

## Optimized functions without unpacking <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

PHP optimizes some internal functions into special opcodes on VM level. Such optimization results in much faster execution compared to calling standard function. This only works when these functions are not invoked with argument unpacking (...).

The list of these functions varies across PHP versions, but is the same as functions that must be referenced by their global name (either by `\` prefix or using `use function`), not a fallback name inside namespaced code.

**Insight Class**: `SlevomatCodingStandard\Sniffs\PHP\OptimizedFunctionsWithoutUnpackingSniff`

## Type cast <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff enforces using shorthand cast operators, forbids use of unset and binary cast operators.

**Insight Class**: `SlevomatCodingStandard\Sniffs\PHP\TypeCastSniff`

## Useless parentheses <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff looks for useless parentheses.

**Insight Class**: `SlevomatCodingStandard\Sniffs\PHP\UselessParenthesesSniff`

## Useless semicolon <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff looks for useless semicolons.

**Insight Class**: `SlevomatCodingStandard\Sniffs\PHP\UselessSemicolonSniff`

## Declare strict types <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff enforces having `declare(strict_types = 1)` at the top of each PHP file.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff`

## Duplicate assignment to variable <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff looks for duplicate assignments to a variable.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Variables\DuplicateAssignmentToVariableSniff`

## Empty comment <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff reports empty comment

**Insight Class**: `SlevomatCodingStandard\Sniffs\Commenting\EmptyCommentSniff`

## Nullable type for null default value <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff checks whether the nullablity `?` symbol is present before each nullable and optional parameter (which are marked as `= null`)

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\NullableTypeForNullDefaultValueSniff`

<details>
    <summary>Example </summary>

```php
function foo(
    int $foo = null, // ? missing
    ?int $bar = null // correct
) {
    // ...
}
```
</details>

## Fixme <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff warns about FIXME comments.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\FixmeSniff`

## Todo <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff warns about TODO comments.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff`

## Forbidden comments <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff reports forbidden comments in descriptions.
Nothing is forbidden by default, the configuration is completely up to the user.
It's recommended to forbid generated or inappropriate messages like:
- `Constructor.`
- `Created by PhpStorm.`

**Insight Class**: `SlevomatCodingStandard\Sniffs\Commenting\ForbiddenCommentsSniff`

<details>
    <summary>Configuration</summary>

```php
    SlevomatCodingStandard\Sniffs\Commenting\ForbiddenCommentsSniff::class => [
        'forbiddenCommentPatterns' => []
    ]
```
</details>

## Inline doc comment declaration <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff reports invalid inline phpDocs with `@var`.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff`

## Disallow Array type hint syntax <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff disallows usage of array type hint syntax (eg. `int[]`, `bool[][]`) in phpDocs in favour of generic type hint syntax (eg. `array<int>`, `array<array<bool>>`).

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff`

## Disallow mixed type hint <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff disallow usage of "mixed" type hint in phpDocs.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff`

## Long type hints <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff enforces using shorthand scalar typehint variants in phpDocs: `int` instead of `integer` and `bool` instead of `boolean`.
This is for consistency with native scalar typehints which also allow shorthand variants only.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\LongTypeHintsSniff`

## Null type hint on last position <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff enforces `null` type hint on last position in annotations.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\NullTypeHintOnLastPositionSniff`

## Type hint declaration <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

See the [official explanation](https://github.com/slevomat/coding-standard/#slevomatcodingstandardtypehintstypehintdeclaration-)

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff`

## Useless constant type hint <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff reports useless `@var` annotation (or whole documentation comment) for constants because the type of constant is always clear.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\UselessConstantTypeHintSniff`

## Useless Inherit doc comment <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff reports documentation comments containing only `{@inheritDoc}` annotation because inheritance is automatic and it's not needed to use a special annotation for it.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Commenting\UselessInheritDocCommentSniff`

## Unused function parameter <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff checks that all function parameters are used in the function body.
One exception is made for empty function bodies or function bodies that only contain comments.
This could be useful for the classes that implement an interface that defines multiple methods but the implementation only needs some of them.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\UnusedFunctionParameterSniff`

## Call time pass by reference <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff ensures that variables are not passed by reference when calling a function.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\CallTimePassByReferenceSniff`

## Deperacted functions <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff discourages the use of deprecated PHP functions.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DeprecatedFunctionsSniff`

## Nullable type declaration <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff verifies that nullable typehints are lacking superfluous whitespace (e.g. `?int`).

**Insight Class**: `PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions\NullableTypeDeclarationSniff`

## Static closure <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff reports closures not using $this that are not declared static.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff`

## Forbidden define functions <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This insight disallow define functions.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions`

## Forbidden functions <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff discourages the use of alias functions.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff`

## Global Keyword <Badge text="^1.0"/> <Badge text="Code\Globally" type="warn"/>

This sniff disallow usage of `global`.

**Insight Class**: `PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\GlobalKeywordSniff`

## Forbiden Globals <Badge text="^1.0"/> <Badge text="Code\Globally" type="warn"/>

This sniff detects globals accesses.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals`

<!--
Insight template
##  <Badge text="^1.0"/> <Badge text="Code\Globally" type="warn"/>

This sniff

**Insight Class**: ``

<details>
    <summary>Configuration</summary>

```php

```
</details>
-->
