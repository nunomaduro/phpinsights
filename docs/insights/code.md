# Code

The following insights are organised in different metrics :

* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Classes` <Badge text="Code\Classes" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Code` <Badge text="Code\Code" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments` <Badge text="Code\Comments" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Functions` <Badge text="Code\Functions" type="warn" vertical="middle"/>
* `NunoMaduro\PhpInsights\Domain\Metrics\Code\Globally` <Badge text="Code\Globally" type="warn" vertical="middle"/>


## Forbidden public property <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff disallows public properties.

**Insight Class**: `ObjectCalisthenics\Sniffs\Classes\ForbiddenPublicPropertySniff`

## Unused private elements <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff detects unused private elements

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

## Class constant visibility <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff requires declaring visibility for all class constants.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff`

## Disallow Late static Binding for constants <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff disallows late static binding for constants.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\DisallowLateStaticBindingForConstantsSniff`

## Modern ClassName reference  <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff reports use of `__CLASS__`, `get_parent_class()`, `get_called_class()`, `get_class()` and `get_class($this)`. Class names should be referenced via `::class` constant when possible.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff`

## Useless Late Static Binding <Badge text="^1.0"/> <Badge text="Code\Classes" type="warn"/>

This sniff reports useless late static binding.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Classes\UselessLateStaticBindingSniff`

## Protected to private <Badge text="^1.10"/> <Badge text="Code\Classes" type="warn"/>

This fixer converts `protected` variables and methods to `private` where possible.

**Insight Class**: `PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer`

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

## Element name minimal length <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/> <Badge text="configurable"/>

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

## Max nesting level <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/> <Badge text="configurable"/>

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

This sniff detects usage of the `eval()` function.

**Insight Class**: `PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\EvalSniff`

## Array indent <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/> <Badge text="configurable"/>

This sniff ensures arrays are correctly indented

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

- Checks against two semi-colons with no executable code in between.
- Checks against an empty PHP open - close tag combination.

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

This sniff detects the use of methods that only call their parent classes's method with the same name and arguments.
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

This sniff discourages the use of the PHP `goto` language construct.

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

This sniff disallows assignments in conditions.

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\AssignmentInConditionSniff`

## Disallow continue without integer operand in switch <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows the use of `continue` without an integer operand in a switch because it emits a warning in PHP 7.3 and higher.

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\DisallowContinueWithoutIntegerOperandInSwitchSniff`

## Disallow empty <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows use of `empty()`.

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\DisallowEmptySniff`

## Disallow short ternary operator <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff disallows the short ternary operator `?:`.

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\DisallowShortTernaryOperatorSniff`

## Disallow Yoda Comparison <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

Yoda conditions decrease code comprehensibility and readability by switching operands around comparison operators forcing the reader to read the code in an unnatural way.

**Insight Class**: `SlevomatCodingStandard\Sniffs\ControlStructures\DisallowYodaComparisonSniff`

## Require Yoda Comparison <Badge text="^1.0"/> <Badge text="not enabled" type="error"/>

This sniff enforces yoda comparison usage.

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

This sniff looks for unused inherited variables passed to closures via `use`.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff`

## Useless Parameter default value <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff looks for useless parameter default value.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Functions\UselessParameterDefaultValueSniff`

## Use from same namespace <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff prohibits uses from the same namespace.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Namespaces\UseFromSameNamespaceSniff`

## Useless Alias <Badge text="^1.0"/> <Badge text="Code\Code" type="warn"/>

This sniff looks for use alias that is the same as the unqualified name.

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

## Nullable type for null default value <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff checks whether the nullability `?` symbol is present before each nullable and optional parameter (which are marked as `= null`)

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

## Void return <Badge text="^1.10"/> <Badge text="Architecture\Functions" type="warn"/>

This fixer adds a `void` return type to functions with missing or empty return statements, but priority is given to `@return` annotations. 

**Insight Class**: `PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer`

<!--
Insight template
##  <Badge text="^1.0"/> <Badge text="Architecture\Traits" type="warn"/>

This sniff

**Insight Class**: ``

<details>
    <summary>Configuration</summary>

```php

```
</details>
-->

## Fixme <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff warns about FIXME comments.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\FixmeSniff`

## Todo <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff warns about TODO comments.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Commenting\TodoSniff`

## Forbidden comments <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/> <Badge text="configurable"/>

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

## Useless Function doc comment <Badge text="^1.12"/> <Badge text="Code\Comments" type="warn"/>

This sniff disallows useless doc comments. If the native method declaration contains everything and the phpDoc does not add anything useful, it's reported as useless.

Some type hints can be enforced to be specified with a contained type, with `traversableTypeHints`. See the [official explanation](https://github.com/slevomat/coding-standard#slevomatcodingstandardcommentinguselessfunctiondoccomment-)

**Insight Class**: `SlevomatCodingStandard\Sniffs\Commenting\UselessFunctionDocCommentSniff`

<details>
    <summary>Configuration</summary>

```php
SlevomatCodingStandard\Sniffs\Commenting\UselessFunctionDocCommentSniff::class => [
    'traversableTypeHints' => []
]
```
</details>

## Disallow Array type hint syntax <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff disallows usage of array type hint syntax (eg. `int[]`, `bool[][]`) in phpDocs in favour of generic type hint syntax (eg. `array<int>`, `array<array<bool>>`).

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff`

## Disallow mixed type hint <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff disallows usage of the "mixed" type hint in phpDocs.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff`

## Long type hints <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff enforces using shorthand scalar type hint variants in phpDocs: `int` instead of `integer` and `bool` instead of `boolean`.
This is for consistency with native scalar type hints which also allow shorthand variants only.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\LongTypeHintsSniff`

## Null type hint on last position <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff enforces `null` type hint on last position in annotations.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\NullTypeHintOnLastPositionSniff`

## Type hint declaration <Badge text=">=1.0 <1.12"/> <Badge text="Code\Comments" type="warn"/>

See the [official explanation](https://github.com/slevomat/coding-standard/tree/5.0.4#slevomatcodingstandardtypehintstypehintdeclaration-)

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\TypeHintDeclarationSniff`

## Parameter Type hint <Badge text="^1.12"/> <Badge text="Code\Comments" type="warn"/>

See the [official explanation](https://github.com/slevomat/coding-standard#slevomatcodingstandardtypehintsparametertypehint-)

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff`

## Property Type hint <Badge text="^1.12"/> <Badge text="Code\Comments" type="warn"/>

See the [official explanation](https://github.com/slevomat/coding-standard#slevomatcodingstandardtypehintspropertytypehint-)

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\PropertyTypeHintSniff`

## Return Type hint <Badge text="^1.12"/> <Badge text="Code\Comments" type="warn"/>

See the [official explanation](https://github.com/slevomat/coding-standard#slevomatcodingstandardtypehintsreturntypehint-)

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff`

## Useless constant type hint <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff reports useless `@var` annotation (or whole documentation comment) for constants because the type of the constant is always clear.

**Insight Class**: `SlevomatCodingStandard\Sniffs\TypeHints\UselessConstantTypeHintSniff`

## Useless Inherit doc comment <Badge text="^1.0"/> <Badge text="Code\Comments" type="warn"/>

This sniff reports documentation comments containing only `{@inheritDoc}` annotation because inheritance is automatic and it's not needed to use a special annotation for it.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Commenting\UselessInheritDocCommentSniff`

## Unused parameter <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff looks for unused parameters.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff`

## Call time pass by reference <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff ensures that variables are not passed by reference when calling a function.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\Functions\CallTimePassByReferenceSniff`

## Deprecated functions <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff discourages the use of deprecated PHP functions.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DeprecatedFunctionsSniff`

## Nullable type declaration <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff verifies that nullable type hints are lacking superfluous whitespace (e.g. `?int`).

**Insight Class**: `PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions\NullableTypeDeclarationSniff`

## Static closure <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff reports closures not using `$this` that are not declared static.

**Insight Class**: `SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff`

## Forbidden define functions <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This insight disallows define functions.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions`

## Forbidden functions <Badge text="^1.0"/> <Badge text="Code\Functions" type="warn"/>

This sniff discourages the use of alias functions.

**Insight Class**: `PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff`

## Global Keyword <Badge text="^1.0"/> <Badge text="Code\Globally" type="warn"/>

This sniff disallow usage of `global`.

**Insight Class**: `PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\GlobalKeywordSniff`

## Forbidden Globals <Badge text="^1.0"/> <Badge text="Code\Globally" type="warn"/>

This sniff detects globals accesses.

**Insight Class**: `NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals`

## Visibility Required <Badge text="^1.10"/> <Badge text="Code\Classes" type="warn"/> <Badge text="configurable"/>

Visibility must be declared on all properties and methods. `abstract` and `final` must be declared before the visibility. `static` must be declared after the visibility.

**Insight Class**: `PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer::class => [
    'elements' => [
        'property',
        'method',
    ],
]
```
</details>

## Ternary to Null Coalescing <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

This fixer enforces using the null coalescing operator `??` where possible.

**Insight Class**: `PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer`

## Combine nested dirname <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

This fixer replaces multiple nested calls of `dirname` with only one call with second `$level` parameter. 

**Insight Class**: `PhpCsFixer\Fixer\FunctionNotation\CombineNestedDirnameFixer`

## Declare Equal normalize <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/> <Badge text="configurable"/>

This fixer normalizes space around the equal sign in declare section. 

**Insight Class**: `PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer::class => [
    'space' => 'none', // possible values ['none', 'single']
]
```
</details>

## Explicit string variable <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

This fixer converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.

**Insight Class**: `PhpCsFixer\Fixer\StringNotation\ExplicitStringVariableFixer`

## New with braces <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

This fixer asserts all instances created with new keyword must be followed by braces.

**Insight Class**: `PhpCsFixer\Fixer\Operator\NewWithBracesFixer`

## No alternative syntax <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

This fixer replaces control structure alternative syntax to use braces.

**Insight Class**: `PhpCsFixer\Fixer\ControlStructure\NoAlternativeSyntaxFixer`

## No mixed echo print <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/> <Badge text="configurable"/>

This fixer enforces either language construct print or echo should be used.

**Insight Class**: `PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer::class => [
    'use' => 'echo' // possibles values ['echo', 'print']
]
```
</details>

## No multiline whitespace around double arrow <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

The Operator `=>` should not be surrounded by multi-line whitespaces.

**Insight Class**: `PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer`

## No short bool cast <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

This fixer asserts short cast bool using double exclamation marks should not be used.

**Insight Class**: `PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer`

## No superfluous Elseif <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

This fixer replaces superfluous `elseif` with `if`.

**Insight Class**: `PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer`

## No unneeded control parentheses <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/> <Badge text="configurable"/>

This fixer removes unneeded parentheses around control statements.

**Insight Class**: `PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer::class => [
    'statements' => [
        'break',
        'clone',
        'continue',
        'echo_print',
        'return',
        'switch_case',
        'yield',
    ],
]
```
</details>

## No useless Else <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

This fixer assert there should not be useless `else` cases.

**Insight Class**: `PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer`

## Normalize index brace <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

Array index should always be written by using square braces.

**Insight Class**: `PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer`

## Object operator without Whitespace <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

There should not be space before or after object `T_OBJECT_OPERATOR ->`.

**Insight Class**: `PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer`

## Short scalar cast <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

Cast `(boolean)` and `(integer)` should be written as `(bool)` and `(int)`, `(double)` and `(real)` as `(float)`, `(binary)` as `(string)`.

**Insight Class**: `PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer`

## Ternary operator spaces <Badge text="^1.10"/> <Badge text="Code\Code" type="warn"/>

This fixer standardizes spaces around ternary operators.

**Insight Class**: `PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer`

## Multiline comment opening closing <Badge text="^1.10"/> <Badge text="Code\Comments" type="warn"/>

DocBlocks must start with two asterisks, multiline comments must start with a single asterisk, after the opening slash. 
Both must end with a single asterisk before the closing slash.

**Insight Class**: `PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer`

## No empty comment <Badge text="^1.10"/> <Badge text="Code\Comments" type="warn"/>

There should not be any empty comments.

**Insight Class**: `PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer`

## No break comment <Badge text="^1.10"/> <Badge text="Code\Comments" type="warn"/> <Badge text="configurable"/>

There must be a comment when fall-through is intentional in a non-empty case body.

**Insight Class**: `PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer::class => [
    'comment_text' => 'no break',
]
```
</details>

## Phpdoc scalar <Badge text="^1.10"/> <Badge text="Code\Comments" type="warn"/> <Badge text="configurable"/>

Scalar types should always be written in the same form. 
`int` not `integer`, `bool` not `boolean`, `float` not `real` or `double`.

**Insight Class**: `PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer`

<details>
    <summary>Configuration</summary>

```php
\PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer::class => [
    'types' => [ 
        'boolean',
        'double',
        'integer',
        'real',
        'str',
    ]    
]
```
</details>

## No spaces after function name <Badge text="^1.10"/> <Badge text="Code\Functions" type="warn"/>

When making a method or function call, there must not be a space between the method or function name and the opening parenthesis.

**Insight Class**: `PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer`

## Return assignment <Badge text="^1.10"/> <Badge text="Code\Functions" type="warn"/>

Local, dynamic and directly referenced variables should not be assigned and directly returned by a function or method.

**Insight Class**: `PhpCsFixer\Fixer\ReturnNotation\ReturnAssignmentFixer`

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
