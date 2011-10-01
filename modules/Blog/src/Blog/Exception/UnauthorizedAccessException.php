<?php

namespace Blog\Exception;

use Blog\Exception,
    DomainException;

class UnauthorizedAccessException extends DomainException implements Exception
{}
