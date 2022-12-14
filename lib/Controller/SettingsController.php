<?php

/**
 * ownCloud - registration
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Bruno Alfred <hello@brunoalfred.me>
 */

namespace OCA\Twigacloudsignup\Controller;

use OCP\IGroup;
use \OCP\IRequest;
use \OCP\AppFramework\Http\DataResponse;
use \OCP\AppFramework\Http;
use \OCP\AppFramework\Controller;
use \OCP\IGroupManager;
use \OCP\IL10N;
use \OCP\IConfig;

class SettingsController extends Controller
{

    /** @var IL10N */
    private $l10n;
    /** @var IConfig */
    private $config;
    /** @var IGroupManager */
    private $groupmanager;
    /** @var string */
    protected $appName;

    public function __construct($appName, IRequest $request, IL10N $l10n, IConfig $config, IGroupManager $groupmanager)
    {
        parent::__construct($appName, $request);
        $this->l10n = $l10n;
        $this->config = $config;
        $this->groupmanager = $groupmanager;
        $this->appName = $appName;
    }

    /**
     * @AdminRequired
     *
     * @param string|null $registered_user_group all newly registered user will be put in this group
     * @param string $additional_hint show Text at user-creation form
     * @param string $phone_verification_hint if filled embed Text in Verification mail send to user
     * @param string $username_policy_regex optional regex to check usernames against a pattern
     * @param bool|null $admin_approval_required newly registered users have to be validated by an admin
     * @param bool|null $phone_is_optional phone address is not required
     * @param bool|null $phone_is_login phone address is forced as user id
     * @return DataResponse
     */
    public function admin(
        ?string $registered_user_group,
        string $additional_hint,
        string $phone_verification_hint,
        string $username_policy_regex,
        ?bool $admin_approval_required,
        ?bool $phone_is_optional,
        ?bool $phone_is_login,
        ?bool $show_fullname,
        ?bool $enforce_fullname,
        ?bool $show_phone,
        ?bool $enforce_phone,
    ) {

        // handle hints
        if (($additional_hint === '') || ($additional_hint === null)) {
            $this->config->deleteAppValue($this->appName, 'additional_hint');
        } else {
            $this->config->setAppValue($this->appName, 'additional_hint', $additional_hint);
        }

        if (($phone_verification_hint === '') || ($phone_verification_hint === null)) {
            $this->config->deleteAppValue($this->appName, 'phone_verification_hint');
        } else {
            $this->config->setAppValue($this->appName, 'phone_verification_hint', $phone_verification_hint);
        }

        //handle regex
        if (($username_policy_regex === '') || ($username_policy_regex === null)) {
            $this->config->deleteAppValue($this->appName, 'username_policy_regex');
        } elseif ((@preg_match($username_policy_regex, '') === false)) {
            // validate regex
            return new DataResponse([
                'data' => [
                    'message' => $this->l10n->t('Invalid username policy regex'),
                ],
                'status' => 'error',
            ], Http::STATUS_BAD_REQUEST);
        } else {
            $this->config->setAppValue($this->appName, 'username_policy_regex', $username_policy_regex);
        }

        $this->config->setAppValue($this->appName, 'admin_approval_required', $admin_approval_required ? 'yes' : 'no');
        $this->config->setAppValue($this->appName, 'phone_is_login', !$phone_is_optional && $phone_is_login ? 'yes' : 'no');
        $this->config->setAppValue($this->appName, 'show_fullname', $show_fullname ? 'yes' : 'no');
        $this->config->setAppValue($this->appName, 'enforce_fullname', $enforce_fullname ? 'yes' : 'no');
        $this->config->setAppValue($this->appName, 'show_phone', $show_phone ? 'yes' : 'no');
        $this->config->setAppValue($this->appName, 'enforce_phone', $enforce_phone ? 'yes' : 'no');


        if ($registered_user_group === null) {
            $this->config->deleteAppValue($this->appName, 'registered_user_group');
            return new DataResponse([
                'data' => [
                    'message' => $this->l10n->t('Saved'),
                ],
                'status' => 'success',
            ]);
        }

        $group = $this->groupmanager->get($registered_user_group);
        if ($group instanceof IGroup) {
            $this->config->setAppValue($this->appName, 'registered_user_group', $registered_user_group);
            return new DataResponse([
                'data' => [
                    'message' => $this->l10n->t('Saved'),
                ],
                'status' => 'success',
            ]);
        }

        return new DataResponse([
            'data' => [
                'message' => $this->l10n->t('No such group'),
            ],
            'status' => 'error',
        ], Http::STATUS_NOT_FOUND);
    }
}
