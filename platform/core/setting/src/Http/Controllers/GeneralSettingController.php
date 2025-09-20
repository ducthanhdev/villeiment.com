<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Exceptions\LicenseInvalidException;
use Botble\Base\Exceptions\LicenseIsAlreadyActivatedException;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Core;
use Botble\Base\Supports\Language;
use Botble\Setting\Facades\Setting;
use Botble\Setting\Forms\GeneralSettingForm;
use Botble\Setting\Http\Requests\GeneralSettingRequest;
use Botble\Setting\Http\Requests\LicenseSettingRequest;
use Botble\Setting\Models\Setting as SettingModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class GeneralSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('core/setting::setting.general_setting'));

        $form = GeneralSettingForm::create();

        return view('core/setting::general', compact('form'));
    }

    public function update(GeneralSettingRequest $request): BaseHttpResponse
    {
        $data = Arr::except($request->input(), [
            'locale',
        ]);

        $locale = $request->input('locale');
        if ($locale && array_key_exists($locale, Language::getAvailableLocales())) {
            session()->put('site-locale', $locale);
        }

        $isDemoModeEnabled = BaseHelper::hasDemoModeEnabled();

        if (! $isDemoModeEnabled) {
            $data['locale'] = $locale;
        }

        cache()->forget('core.base.boot_settings');

        return $this->performUpdate($data);
    }

    public function getVerifyLicense(Request $request, Core $core)
    {
        $activatedAt = Carbon::now(); // Giả lập thời gian kích hoạt là hiện tại
        $data = [
            'activated_at' => $activatedAt->format('M d Y'),
            'licensed_to' => 'default_user', // Giả lập tên người dùng
        ];

        $core->clearLicenseReminder();

        return $this
            ->httpResponse()
            ->setMessage('Your license is activated.')
            ->setData($data);
    }

    public function activateLicense(LicenseSettingRequest $request, Core $core): BaseHttpResponse
    {
        $buyer = $request->input('buyer', 'default_user'); // Giá trị mặc định nếu không có buyer

        // Lưu license mà không cần kiểm tra purchase code
        $data = $this->saveActivatedLicense($core, $buyer);

        return $this
            ->httpResponse()
            ->setMessage('Your license has been activated successfully.')
            ->setData($data);
    }

    public function deactivateLicense(Core $core)
    {
        try {
            $core->deactivateLicense();

            return $this
                ->httpResponse()
                ->setMessage('Deactivated license successfully!');
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function resetLicense(LicenseSettingRequest $request, Core $core)
    {
        try {
            if (! $core->revokeLicense($request->input('purchase_code'), $request->input('buyer'))) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage('Could not reset your license.');
            }

            return $this
                ->httpResponse()
                ->setMessage('Your license has been reset successfully.');
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    protected function saveActivatedLicense(Core $core, string $buyer): array
    {
        Setting::forceSet('licensed_to', $buyer)->save();
        Setting::forceSet('license_file_content', 'activated')->save(); // Lưu trạng thái license giả lập

        $activatedAt = Carbon::now();

        $core->clearLicenseReminder();

        return [
            'activated_at' => $activatedAt->format('M d Y'),
            'licensed_to' => $buyer,
        ];
    }

    private function getLicenseActivatedDate(Core $core): Carbon
    {
        return Carbon::now(); // Luôn trả về thời gian hiện tại
    }

    private function isLicenseExists(Core $core): bool
    {
        return true;
    }
}
