<?php

namespace Jhoff\EloquentExtraAttributes\Tests\Feature;

use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Jhoff\EloquentExtraAttributes\Models\ExtraAttribute;
use Jhoff\EloquentExtraAttributes\Models\User;
use Jhoff\EloquentExtraAttributes\Tests\ObjectPrybar;
use Jhoff\EloquentExtraAttributes\Tests\TestCase;
use Jhoff\EloquentExtraAttributes\Traits\ExtraAttributes;

class ExtraAttributeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    /** @test  */
    public function ensure_test_model_uses_trait_and_is_empty_on_initialization()
    {
        $this->assertContains(ExtraAttributes::class, class_uses_recursive($this->user));
        $this->assertInstanceOf(MorphOne::class, $this->user->extraAttributes());

        $this->assertInstanceOf(ExtraAttribute::class, $this->user->extraAttributes);
        $this->assertInstanceOf(ArrayObject::class, $this->user->extraAttributes->attrs);
        $this->assertEmpty($this->user->extraAttributes->attrs);
    }

    public function primitives()
    {
        return [
            'boolean' => [true],
            'string' => ['foobar'],
            'integer' => [12345],
            'float' => [12.345],
            'array' => [['abc', 123]],
            'null' => [null],
        ];
    }

    /**
     * @test
     * @dataProvider primitives
     */
    public function model_can_read_write_primitives_through_relation($value)
    {
        // read / isset / write ( memory )
        $this->user->extraAttributes->memory = $value;
        $this->assertTrue(isset($this->user->extraAttributes->memory));
        $this->assertEquals($value, $this->user->extraAttributes->memory);

        // read / isset / write ( database )
        $this->user->extraAttributes->database = $value;
        $this->user->save();
        $this->assertTrue(isset($this->user->refresh()->extraAttributes->database));
        $this->assertEquals($value, $this->user->refresh()->extraAttributes->database);

        // remove ( memory )
        unset($this->user->extraAttributes->memory);
        $this->assertFalse(isset($this->user->extraAttributes->memory));

        // remove ( database )
        unset($this->user->extraAttributes->database);
        $this->user->save();
        $this->assertFalse(isset($this->user->refresh()->extraAttributes->database));
    }

    /**
     * @test
     * @dataProvider primitives
     */
    public function model_can_read_write_primitives_directly($value)
    {
        (new ObjectPrybar($this->user))
            ->setProperty('allowedExtras', ['memory', 'database']);

        // read / isset / write ( memory )
        $this->user->memory = $value;
        $this->assertTrue(isset($this->user->extraAttributes->memory));
        $this->assertEquals($value, $this->user->extraAttributes->memory);

        // read / isset / write ( database )
        $this->user->database = $value;
        $this->user->save();
        $this->assertTrue(isset($this->user->refresh()->extraAttributes->database));
        $this->assertEquals($value, $this->user->refresh()->extraAttributes->database);

        // remove ( memory )
        unset($this->user->memory);
        $this->assertFalse(isset($this->user->extraAttributes->memory));

        // remove ( database )
        unset($this->user->database);
        $this->user->save();
        $this->assertFalse(isset($this->user->refresh()->extraAttributes->database));
    }

    protected function setUp(): void
    {
        parent::setUp();

        // users have extra attributes, let's test them!
        $this->user = User::factory()->create();
    }
}
