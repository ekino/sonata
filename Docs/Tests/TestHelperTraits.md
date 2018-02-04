Test Helper Traits
==================

Tests are a big part of the development process.
In order to facilitate their implementation, here is a list
of traits that you can use.

ClassTestHelper
---------------
It contains 1 public static functions.
### getInstanceWithId
Return an instance of a class where the id is set.
Useful to mock Doctrine entities.

```php
$foo = ClassTestHelper::getInstanceWithId(Foo::class, 10);
echo $foo->getId() // 10
```

TranslationTestHelper
---------------------
It contains 1 public static functions.
### getTranslationString
Takes the same parameters as the `trans` function of Symfony `TranslatorInterface` and return a formatted string.
```php
echo TranslationTestHelper::getTranslationString('foo', [], 'AppBundle');
// Key    : foo
// Params : {}
// Domain : AppBundle
```

MockBuilderTrait
----------------
It contains 1 public functions.
### mockTranslator
Mock Symfony `TranslatorInterface` and assign `getTranslationString` as callback to the `trans method.

```php
// Init the test
$translatorMock = $this->mockTranslator();
$myTestedService = new TestedService($translatorMock);
$result = $myTestedService->executeFunction();

// Validate the result
$this->assertSame(
  $result['translation_to_validate'], 
  TranslationTestHelper::getTranslationString('translation_key', $paramters, 'domain')
);
```

AdminFormFieldTrait
-------------------
It contains 2 protected functions. It must be used in a class that extends
`\PHPUnit_Framework_TestCase`.
### mockFormMapper
Returns a mock of the `FormMapper` that 
stores all added fields in a public property fields (it also skips the groups). 

### expectInOrder
Use that previously created mock to test the
`configureFormFields` method of the admin.

```php
public function testConfigureFormFields()
{
  // ...
  $formMapper = $this->mockFormMapper($admin);
  $this->expectInOrder($formMapper, [
    ['title', TextType::class],
    ['position', NumberType::class],
  ]);
}
```

AdminTestCase
-------------
It contains 1 protected functions. It must be used in a class that extends
`\PHPUnit_Framework_TestCase`.
### mockDefaultServices
The trait contains already all properties to store the services 
passed to an admin by Sonata compiler pass.
This method create all the mocks and add them to the admin.

```php
// ...
$admin = new MyAdmin();
$this->mockDefaultServices($admin);
// If you need to override a service
$admin->setMyOverride($myOverride);

```

FragmentFormFieldTestTrait
--------------------------
It contains 1 protected functions. It must be used in a class that extends
`\PHPUnit_Framework_TestCase`.
### expectInOrder
Usually, in the `buildForm` of the `FragmentService` you will
add fields to the `setting` hey of the form. This trait will
help you validating the fields created by calling the `buildForm` method
of the `FragmentService`.

```php
// ...
public function testBuildForm()
{
  $fragment = new Fragment();
  $fragmentService = new MyFragmentService();
  // The $fragment paramerter is optional here
  // but sometimes, the result of the buildForm
  // depend on its values.
  $this->expectInOrder(
    $fragmentService, 
    [
      ['title', TextType::class],
      ['position', NumberType::class],
    ], 
    $fragment
  );
}
```
