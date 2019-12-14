<?php


namespace App\Constants;


class ResponseMessage
{
    const ERROR = "There seems to be a code error with the method being implemented. Do you mind trying later?";
    const INVALID_PARAMS = 'Invalid Parameters';
    const COULDNT_CREATE_USER = 'An error occurred, we could not create this user.';
    const USER_NOT_FOUND = 'User can not be found';
    const CREATED_USER = 'User was created successfully.';
    const LOGIN_FAILED = 'Login failed.';
    const LOGIN_SUCCESSFUL = 'Login Successful.';
    const LOGOUT_SUCCESSFUL = 'Successfully Logged out.';
    const PASSWORD_RESET_LINK_SENT = 'A link for you to reset your password has been sent to you.';
    const RESOURCE_NOT_FOUND = '%s not found.';
    const RESOURCE_CREATED = '%s has been created.';
    const PASSWORD_TOKEN_INVALID = 'This password token is invalid.';
    const PASSWORD_RESET_SUCCESSFUL = 'The password reset operation was successful.';
    const INVALID_CREDENTIALS_SUPPLIED = 'Invalid credentials.';
    const CURRENT_PASSWORD_INVALID = 'Your current password is invalid.';
    const PASSWORD_CHANGE_SUCCESSFUL = 'Password change was successful.';
    const PICTURE_UPLOAD_SUCCESSFUL = '%s upload was successful';
    const ERROR_OCCURRED = 'An error occurred, please try again later.';
    const USER_CANT_TAKE_NEW_ROLE = 'This user already exist and can not accept this new role.';
    const UPDATE_WAS_SUCCESSFUL = '%s update was successful';
    const CREATE_WAS_SUCCESSFUL = '%s create was successful';
    const DELETE_WAS_SUCCESSFUL = '%s delete was successful';
    const RESOURCE_WAS_SUCCESSFUL = '%s was successful';
 }