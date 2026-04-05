<?php

class FormErrors
{
    private $name_error = null;
    private $email_error = null;
    private $phone_number_error = null;
    private $customer_id_error = null;
    private $employee_id_error = null;
    private $out_of_bounds_error = null;
    private $date_error = null;
    private $success_message = null;

    public function __construct() {}

    public function setNameError($value)
    {

        if (!preg_match("/^[a-zA-Z-' ]*$/", $value)) {
            $name_error = "Only letters and white space allowed";
        }
    }


    public function setEmailError($value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Invalid email format";
        }
    }

    public function setPhoneNumberError($value)
    {
        if (!preg_match("/\d{3}-\d{3}-\d{4}/", $value)) {
        }
    }

    public function setCustomerIdError($value) {}

    public function setEmployeeIdError($value) {}

    public function setOutOfBoundsError($value) {}

    public function setDateError($value) {}

    public function setSuccessMessage($value) {}

    public function is_error(): bool
    {
        return true;
    }

    public function getEmailError()
    {
        return $this->email_error;
    }

    public function getPhoneNumberError()
    {
        return $this->phone_number_error;
    }

    public function getCustomerIdError()
    {
        return $this->customer_id_error;
    }

    public function getEmployeeIdError()
    {
        return $this->employee_id_error;
    }

    public function getOutOfBoundsError()
    {
        return $this->out_of_bounds_error;
    }

    public function getDateError()
    {
        return $this->date_error;
    }

    public function getSuccessMessage()
    {
        return $this->success_message;
    }
}
