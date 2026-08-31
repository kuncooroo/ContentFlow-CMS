<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\PureUnitTestCase;

/**
 * Meta-regression gate: ensures critical SRS test files remain in the suite.
 */
class RegressionCoverageTest extends PureUnitTestCase
{
    /**
     * @return array<string, array{0: string}>
     */
    public static function criticalTestFileProvider(): array
    {
        $files = [
            'auth_login' => 'tests/Feature/Auth/LoginTest.php',
            'auth_password_reset' => 'tests/Feature/Auth/PasswordResetTest.php',
            'auth_logout' => 'tests/Feature/Auth/LogoutTest.php',
            'users_management' => 'tests/Feature/Livewire/Admin/Users/UserManagementTest.php',
            'roles_management' => 'tests/Feature/Admin/Roles/RoleManagementTest.php',
            'permission_matrix' => 'tests/Feature/Authorization/PermissionMatrixTest.php',
            'posts_crud' => 'tests/Feature/Admin/Posts/PostCrudTest.php',
            'posts_publishing' => 'tests/Feature/Admin/Posts/PostPublishingTest.php',
            'scheduled_command' => 'tests/Feature/Console/PublishScheduledPostsCommandTest.php',
            'pages_crud' => 'tests/Feature/Admin/Pages/PageCrudTest.php',
            'categories' => 'tests/Feature/Admin/Categories/CategoryManagementTest.php',
            'tags' => 'tests/Feature/Admin/Tags/TagManagementTest.php',
            'media' => 'tests/Feature/Admin/Media/MediaManagementTest.php',
            'comments' => 'tests/Feature/Comments/CommentSubmissionTest.php',
            'menus' => 'tests/Feature/Admin/Menus/MenuManagementTest.php',
            'seo_management' => 'tests/Feature/Admin/Seo/SeoManagementTest.php',
            'settings' => 'tests/Feature/Admin/Settings/SiteSettingsManagementTest.php',
            'dashboard' => 'tests/Feature/Admin/Dashboard/DashboardOverviewTest.php',
            'audit_ui' => 'tests/Feature/Admin/Audit/ActivityLogIndexTest.php',
            'audit_actions' => 'tests/Feature/Audit/UserActionAuditTest.php',
            'notifications' => 'tests/Feature/Notifications/ResetPasswordNotificationTest.php',
            'ui_feedback' => 'tests/Feature/UiFeedback/AdminFlashFeedbackTest.php',
            'security_csrf' => 'tests/Feature/Security/CsrfProtectionTest.php',
            'security_xss' => 'tests/Feature/Security/XssOutputTest.php',
            'public_visibility' => 'tests/Feature/Public/PublicContentVisibilityTest.php',
        ];

        $result = [];

        foreach ($files as $name => $path) {
            $result[$name] = [$path];
        }

        return $result;
    }

    #[DataProvider('criticalTestFileProvider')]
    public function test_critical_regression_file_exists(string $relativePath): void
    {
        $this->assertFileExists(dirname(__DIR__, 3).DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath));
    }
}
