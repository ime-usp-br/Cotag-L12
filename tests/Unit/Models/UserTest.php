<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the User model has the correct fillable attributes.
     */
    public function test_user_model_has_correct_fillable_attributes(): void
    {
        $user = new User();
        $expectedFillable = [
            'name',
            'email',
            'password',
            'codpes',
        ];
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /**
     * Test if the User model has the correct hidden attributes.
     */
    public function test_user_model_has_correct_hidden_attributes(): void
    {
        $user = new User();
        $expectedHidden = [
            'password',
            'remember_token',
        ];
        $this->assertEquals($expectedHidden, $user->getHidden());
    }

    /**
     * Test if the User model has the correct casts.
     * This specifically addresses AC4 of Issue #1.
     */
    public function test_user_model_has_correct_casts(): void
    {
        $user = new User();
        $expectedCasts = [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'codpes' => 'integer', // AC4: Verify codpes is cast to integer
            'id' => 'int', // Default primary key cast
        ];
        $this->assertEquals($expectedCasts, $user->getCasts());
    }

    /**
     * Test if the User model uses the HasFactory trait.
     */
    public function test_user_model_uses_has_factory_trait(): void
    {
        $this->assertContains(
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
            class_uses_recursive(User::class)
        );
    }

    /**
     * Test if the User model uses the Notifiable trait.
     */
    public function test_user_model_uses_notifiable_trait(): void
    {
        $this->assertContains(Notifiable::class, class_uses_recursive(User::class));
    }

    /**
     * Test if the User model uses the HasRoles trait from Spatie.
     * This addresses AC5 of Issue #1.
     */
    public function test_user_model_uses_has_roles_trait(): void
    {
        $this->assertContains(HasRoles::class, class_uses_recursive(User::class));
    }

    /**
     * Test if the User model uses the HasSenhaunica trait.
     * This addresses AC6 of Issue #1.
     */
    public function test_user_model_uses_has_senhaunica_trait(): void
    {
        $this->assertContains(HasSenhaunica::class, class_uses_recursive(User::class));
    }

    /**
     * Test if the User model implements MustVerifyEmail.
     */
    public function test_user_model_implements_must_verify_email(): void
    {
        $this->assertInstanceOf(\Illuminate\Contracts\Auth\MustVerifyEmail::class, new User());
    }

    /**
     * Test the 'codpes' attribute casting.
     * Creates a user with a string 'codpes' and checks if it's retrieved as an integer.
     */
    public function test_codpes_attribute_is_cast_to_integer(): void
    {
        $user = User::factory()->create(['codpes' => '1234567']);
        $retrievedUser = User::find($user->id);

        $this->assertIsInt($retrievedUser->codpes);
        $this->assertSame(1234567, $retrievedUser->codpes);

        // Test with null codpes
        $userNullCodpes = User::factory()->create(['codpes' => null]);
        $retrievedUserNullCodpes = User::find($userNullCodpes->id);
        $this->assertNull($retrievedUserNullCodpes->codpes);
    }
}