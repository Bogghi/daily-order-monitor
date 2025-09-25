<?php

namespace App\Utility;

class Result
{
    public array $data;
    public int $statusCode = 200;
    private const string KO = 'KO';
    private const string OK = 'OK';

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function setInvalidParameters(?array $parameters = null): self
    {
        $this->data['status'] = self::KO;
        $this->data['message'] = 'Invalid parameters ';
        $this->statusCode = 400;
        if($parameters) {
            $this->data['message'] .= implode(',', $parameters);
        }
        return $this;
    }

    public function setUnauthorized(): self
    {
        $this->data['status'] = self::KO;
        $this->data['message'] = 'Unauthorized';
        $this->statusCode = 401;

        return $this;
    }

    public function setSuccessResult(?array $array = null): self
    {
        $this->data['status'] = self::OK;

        if($array) {
            $this->data = [...$this->data, ...$array];
        }

        return $this;
    }

    public function setData(?array $array): self
    {
        $this->data = [...$this->data, ...$array];
        return $this;
    }

    public function setGenericError(mixed $data = null): self
    {
        $this->data['status'] = self::KO;
        $this->statusCode = 500;
        $this->data['message'] = 'An error occurred '.json_encode($data, JSON_UNESCAPED_UNICODE);

        return $this;
    }
}