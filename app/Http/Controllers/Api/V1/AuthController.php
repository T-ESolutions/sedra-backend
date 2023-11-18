<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Interfaces\V1\AuthRepositoryInterface;
use App\Http\Requests\V1\User\Auth\ChangePasswordRequest;
use App\Http\Requests\V1\User\Auth\checkEmailCodeToUpdateRequest;
use App\Http\Requests\V1\User\Auth\CheckEmailToUpdateRequest;
use App\Http\Requests\V1\User\Auth\checkPhoneCodeToUpdateRequest;
use App\Http\Requests\V1\User\Auth\CheckPhoneToUpdateRequest;
use App\Http\Requests\V1\User\Auth\ForgetPasswordRequest;
use App\Http\Requests\V1\User\Auth\UpdateProfileRequest;
use App\Http\Requests\V1\User\Auth\ResendCodeRequest;
use App\Http\Requests\V1\User\Auth\SignUpRequest;
use App\Http\Requests\V1\User\Auth\VerifyRequest;
use App\Http\Requests\V1\User\Auth\LogInRequest;
use App\Http\Resources\V1\User\UsersResources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use JWTAuth;

class AuthController extends Controller
{
    protected $userAuthRepository;

    public function __construct(AuthRepositoryInterface $userAuthRepository)
    {
        $this->userAuthRepository = $userAuthRepository;
    }

    public function unauthrized(Request $request)
    {
        return response()->json(msg(not_authoize(), trans('lang.not_authorize')));
    }

    public function login(LogInRequest $request)
    {
        $data = $request->validated();
        $data = $this->userAuthRepository->logIn($data);

        if (is_string($data)) {
            if ($data == "phoneOrPasswordIncorrect") {
                return response()->json(msg(failed(), trans('lang.phoneOrPasswordIncorrect')));
//            } elseif ($data == "suspended") {
//                return response()->json(msg(suspend(), trans('lang.suspended')));
            } elseif ($data == "not_active") {
                return response()->json(msg(not_active(), trans('lang.not_active')));
            } elseif ($data == "email_not_verified") {
                return response()->json(msg(not_acceptable(), trans('lang.suspended')));
            }
        } else {
            $data = (new UsersResources($data))->token($data->jwt);
            return response()->json(msgdata(success(), trans('lang.success'), $data));
        }
    }

    public function signUp(SignUpRequest $request)
    {
        $data = $request->validated();
        $result = $this->userAuthRepository->signUp($data);
        return response()->json(msgdata(success(), trans('lang.CodeSent'), $result));
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $data = $request->validated();
        $this->userAuthRepository->sendCode($data['email'], "forget");
        return response()->json(msg(success(), trans('lang.CodeSent')));

    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->validated();
        $result = $this->userAuthRepository->changePassword($data);
        if (!$result) {
            return response()->json(msg(failed(), trans('lang.old_passwordError')));
        }
        $data = (new UsersResources($result));
        return response()->json(msgdata(success(), trans('lang.passwordChangedSuccess'), $data));
    }

    public function profile()
    {
        $user = JWTAuth::user();
        $data = (new UsersResources($user));
        return response()->json(msgdata(success(), trans('lang.success'), $data));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        $this->userAuthRepository->updateProfile($data);
        return response()->json(msg(success(), trans('lang.success')));
    }

    public function checkEmailToUpdate(CheckEmailToUpdateRequest $request)
    {
        $data = $request->validated();
        $this->userAuthRepository->checkEmailToUpdate($data);
        return response()->json(msg(success(), trans('lang.success')));
    }

    public function checkPhoneToUpdate(CheckPhoneToUpdateRequest $request)
    {
        $data = $request->validated();
        $this->userAuthRepository->checkPhoneToUpdate($data);
        return response()->json(msg(success(), trans('lang.success')));
    }

    public function checkEmailCodeToUpdate(checkEmailCodeToUpdateRequest $request)
    {
        $data = $request->validated();
        $user = $this->userAuthRepository->checkEmailCodeToUpdate($data);
        if ($user == false) {
            return response()->json(msg(failed(), trans('lang.code_not_valid')));
        } else {
            return response()->json(msgdata(success(), trans('lang.success'), $user));
        }
    }

    public function checkPhoneCodeToUpdate(checkPhoneCodeToUpdateRequest $request)
    {
        $data = $request->validated();
        $user = $this->userAuthRepository->checkPhoneCodeToUpdate($data);
        if ($user == false) {
            return response()->json(msg(failed(), trans('lang.code_not_valid')));
        } else {
            return response()->json(msgdata(success(), trans('lang.success'), $user));
        }
    }

    public function verify(VerifyRequest $request)
    {
        $data = $request->validated();

        $result = $this->userAuthRepository->verify($data);
        if ($result) {
            if (is_string($result) && $result = "suspended") {
                return response()->json(msg(suspend(), trans('lang.suspended')));
            }
            $data = (new UsersResources($result));
            return response()->json(msgdata(success(), trans('lang.Verified_success'), $data));
        }
        return response()->json(msg(failed(), trans('lang.codeError')));
    }

    public function resendCode(ResendCodeRequest $request)
    {
        $data = $request->validated();

        $this->userAuthRepository->resendCode($data);
        return response()->json(msg(success(), trans('lang.success')));

    }

    public function logout()
    {
        auth()->logout();
        return response()->json(msg(success(), trans('lang.logout_s')));

    }

}
