<?php
namespace Tests\AppBundle;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DomCrawler\Crawler;

class ApiTestCase extends KernelTestCase
{
    private static $staticClient;

    /**
     * @var array
     */
    private static $history = [];

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ConsoleOutput
     */
    private $consoleOutput;

    /**
     * @var FormatterHelper
     */
    private $formatterHelper;

    private $responseAsserter;

    public static function setUpBeforeClass()
    {
        $handler = HandlerStack::create();

        $handler->push(Middleware::history(self::$history));
        $handler->push(Middleware::mapRequest(function(RequestInterface $request) {
            $path = $request->getUri()->getPath();
            if (strpos($path, '/app_test.php') !== 0) {
                $path = '/app_test.php' . $path;
            }
            $uri = $request->getUri()->withPath($path);

            return $request->withUri($uri);
        }));

        self::$staticClient = new Client([
            'base_uri' => getenv('TEST_BASE_URL'),
            'http_errors' => false,
            'handler' => $handler
        ]);

        self::bootKernel();
    }

    protected function setUp()
    {
        $this->client = self::$staticClient;
        // reset the history
        self::$history = [];

        $this->purgeDatabase();
    }

    /**
     * Clean up Kernel usage in this test.
     */
    protected function tearDown()
    {
        // purposefully not calling parent class, which shuts down the kernel
    }

    protected function onNotSuccessfulTest($e)
    {
        if ($lastResponse = $this->getLastResponse()) {
            $this->printDebug('');
            $this->printDebug('<error>Failure!</error> when making the following request:');
            $this->printLastRequestUrl();
            $this->printDebug('');

            $this->debugResponse($lastResponse);
        }

        throw $e;
    }

    private function purgeDatabase()
    {
        $purger = new ORMPurger($this->getService('doctrine')->getManager());
        $purger->purge();
    }

    protected function getService($id)
    {
        return self::$kernel->getContainer()
            ->get($id);
    }

    protected function printLastRequestUrl()
    {
        $lastRequest = $this->getLastRequest();

        if ($lastRequest) {
            $this->printDebug(sprintf('<comment>%s</comment>: <info>%s</info>', $lastRequest->getMethod(), $lastRequest->getUri()));
        } else {
            $this->printDebug('No request was made.');
        }
    }

    protected function debugResponse(ResponseInterface $response)
    {
        foreach ($response->getHeaders() as $name => $values) {
            $this->printDebug(sprintf('%s: %s', $name, implode(', ', $values)));
        }
        $body = (string) $response->getBody();

        $contentType = $response->getHeader('Content-Type');
        $contentType = $contentType[0];
        if ($contentType == 'application/json' || strpos($contentType, '+json') !== false) {
            $this->printJson($body);
        } else {
            $this->printHtml($response, $body);
        }
    }

    /**
     * Print a message out - useful for debugging
     *
     * @param $string
     */
    protected function printDebug($string)
    {
        if ($this->consoleOutput === null) {
            $this->consoleOutput = new ConsoleOutput();
        }

        $this->consoleOutput->writeln($string);
    }

    /**
     * Print a debugging message out in a big red block
     *
     * @param $string
     */
    protected function printErrorBlock($string)
    {
        if ($this->formatterHelper === null) {
            $this->formatterHelper = new FormatterHelper();
        }
        $output = $this->formatterHelper->formatBlock($string, 'bg=red;fg=white', true);

        $this->printDebug($output);
    }

    private function getLastRequest(): RequestInterface
    {
        if (empty(self::$history)) {
            return null;
        }

        $history = self::$history;

        $last = array_pop($history);

        return $last['request'];
    }

    private function getLastResponse()
    {
        if (empty(self::$history)) {
            return null;
        }

        $history = self::$history;

        $last = array_pop($history);

        return $last['response'];
    }

    protected function createUser($name, $plainPassword = 'foo')
    {
        $user = new User();
        $user->setName($name);
        $username = (str_replace(' ', '-', strtolower($name)));
        $user->setUsername($username);
        $user->setEmail($username.'@foo.com');
        $password = $this->getService('security.password_encoder')
            ->encodePassword($user, $plainPassword);
        $user->setPassword($password);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function getAuthorizedHeaders($username, $headers = [])
    {
        $token = $this->getService('lexik_jwt_authentication.encoder')
            ->encode(['username' => $username]);

        $headers['Authorization'] = 'Bearer '.$token;

        return $headers;
    }

    /**
     * @return ResponseAsserter
     */
    protected function asserter()
    {
        if ($this->responseAsserter === null) {
            $this->responseAsserter = new ResponseAsserter();
        }

        return $this->responseAsserter;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

    /**
     * Call this when you want to compare URLs in a test
     *
     * (since the returned URL's will have /app_test.php in front)
     *
     * @param string $uri
     * @return string
     */
    protected function adjustUri($uri)
    {
        return '/app_test.php'.$uri;
    }

    private function printJson($body)
    {
        $data = json_decode($body);
        if ($data === null) {
            // invalid JSON!
            $this->printDebug($body);
        } else {
            // valid JSON, print it pretty
            $this->printDebug(json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    private function printHtml(ResponseInterface $response, $body)
    {
        $isValidHtml = strpos($body, '</body>') !== false;

        if ($isValidHtml) {
            $this->printDebug('');
            $crawler = new Crawler($body);

            // very specific to Symfony's error page
            $isError = $crawler->filter('#traces-0')->count() > 0
                || strpos($body, 'looks like something went wrong') !== false;
            if ($isError) {
                $this->printDebug('There was an Error!');
                $this->printDebug('');
            } else {
                $this->printDebug('HTML Summary (h1 and h2):');
            }

            $this->printHeaderTags($crawler, $isError);

            $profilerUrl = $response->getHeader('X-Debug-Token-Link');
            if (!empty($profilerUrl)) {
                $fullProfilerUrl = $response->getHeader('Host').$profilerUrl[0];
                $this->printDebug('');
                $this->printDebug(sprintf(
                    'Profiler URL: <comment>%s</comment>',
                    $fullProfilerUrl
                ));
            }

            // an extra line for spacing
            $this->printDebug('');
        } else {
            $this->printDebug($body);
        }

        return;
    }

    private function printHeaderTags(Crawler $crawler, bool $isError)
    {
        foreach ($crawler->filter('h1, h2')->extract(['_text']) as $header) {
            // avoid these meaningless headers
            if (strpos($header, 'Stack Trace') !== false) {
                continue;
            }
            if (strpos($header, 'Logs') !== false) {
                continue;
            }

            // remove line breaks so the message looks nice
            $header = str_replace("\n", ' ', trim($header));
            // trim any excess whitespace "foo   bar" => "foo bar"
            $header = preg_replace('/(\s)+/', ' ', $header);

            if ($isError) {
                $this->printErrorBlock($header);
            } else {
                $this->printDebug($header);
            }
        }

        return;
    }
}