<?php

namespace Tests\Unit\Listeners;

use App\Listeners\LogMailDeliveryFailure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\PureUnitTestCase;

class LogMailDeliveryFailureTest extends PureUnitTestCase
{
    #[DataProvider('sensitiveMessageProvider')]
    public function test_sensitive_values_are_redacted_from_log_messages(string $input, string $expectedFragment): void
    {
        $listener = new LogMailDeliveryFailure;
        $method = new \ReflectionMethod(LogMailDeliveryFailure::class, 'sanitize');
        $method->setAccessible(true);

        $sanitized = $method->invoke($listener, $input);

        $this->assertNotNull($sanitized);
        $this->assertStringContainsString($expectedFragment, $sanitized);
        $this->assertStringNotContainsString('super-secret-password', $sanitized);
        $this->assertStringNotContainsString('smtp-password-value', $sanitized);
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function sensitiveMessageProvider(): array
    {
        return [
            'password in message' => [
                'Authentication failed for password=super-secret-password',
                '[redacted]',
            ],
            'smtp credential in message' => [
                'Could not authenticate using smtp-password-value',
                '[redacted]',
            ],
        ];
    }
}
