<?php

namespace MedeirosDev\Arquivei\Stack;

use Closure;
use Psr\Http\Message\MessageInterface;
use MedeirosDev\Arquivei\Licenses\License;

class LicenseMiddleware
{
    /**
     * @var License
     */
    protected $license;

    /**
     * LicenseMiddleware constructor.
     *
     * @param License $license
     */
    public function __construct(License $license)
    {
        $this->license = $license;
    }

    /**
     * @param callable $next
     *
     * @return Closure
     */
    public function __invoke(callable $next)
    {
        return function (MessageInterface $message, array $options) use ($next) {
            return $next($this->addLicense($message), $options);
        };
    }

    /**
     * @param MessageInterface $request
     *
     * @return MessageInterface
     */
    protected function addLicense(MessageInterface $request)
    {
        $query = $this->license->getHeadersParameters($request);

        if (count($query)) {
            foreach ($query as $key => $value) {
                $request = $request->withHeader($key, $value);
            }
        }

        return $request;
    }
}
