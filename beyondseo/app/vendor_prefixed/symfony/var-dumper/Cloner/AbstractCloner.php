<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BeyondSEODeps\Symfony\Component\VarDumper\Cloner;

use BeyondSEODeps\Symfony\Component\VarDumper\Caster\Caster;
use BeyondSEODeps\Symfony\Component\VarDumper\Exception\ThrowingCasterException;

/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = [
        '__PHP_Incomplete_Class' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\Caster', 'castPhpIncompleteClass'],

        'BeyondSEODeps\Symfony\Component\VarDumper\Caster\CutStub' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'],
        'BeyondSEODeps\Symfony\Component\VarDumper\Caster\CutArrayStub' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'castCutArray'],
        'BeyondSEODeps\Symfony\Component\VarDumper\Caster\ConstStub' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'],
        'BeyondSEODeps\Symfony\Component\VarDumper\Caster\EnumStub' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'castEnum'],

        'Fiber' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\FiberCaster', 'castFiber'],

        'Closure' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClosure'],
        'Generator' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castGenerator'],
        'ReflectionType' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castType'],
        'ReflectionAttribute' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castAttribute'],
        'ReflectionGenerator' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReflectionGenerator'],
        'ReflectionClass' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClass'],
        'ReflectionClassConstant' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClassConstant'],
        'ReflectionFunctionAbstract' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castFunctionAbstract'],
        'ReflectionMethod' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castMethod'],
        'ReflectionParameter' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castParameter'],
        'ReflectionProperty' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castProperty'],
        'ReflectionReference' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReference'],
        'ReflectionExtension' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castExtension'],
        'ReflectionZendExtension' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castZendExtension'],

        'BeyondSEODeps\Doctrine\Common\Persistence\ObjectManager' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'BeyondSEODeps\Doctrine\Common\Proxy\Proxy' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castCommonProxy'],
        'BeyondSEODeps\Doctrine\ORM\Proxy\Proxy' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castOrmProxy'],
        'BeyondSEODeps\Doctrine\ORM\PersistentCollection' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castPersistentCollection'],
        'BeyondSEODeps\Doctrine\Persistence\ObjectManager' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],

        'DOMException' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castException'],
        'DOMStringList' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMNameList' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMImplementation' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castImplementation'],
        'DOMImplementationList' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMNode' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castNode'],
        'DOMNameSpaceNode' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castNameSpaceNode'],
        'DOMDocument' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDocument'],
        'DOMNodeList' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMNamedNodeMap' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMCharacterData' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castCharacterData'],
        'DOMAttr' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castAttr'],
        'DOMElement' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castElement'],
        'DOMText' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castText'],
        'DOMTypeinfo' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castTypeinfo'],
        'DOMDomError' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDomError'],
        'DOMLocator' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castLocator'],
        'DOMDocumentType' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDocumentType'],
        'DOMNotation' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castNotation'],
        'DOMEntity' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castEntity'],
        'DOMProcessingInstruction' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castProcessingInstruction'],
        'DOMXPath' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DOMCaster', 'castXPath'],

        'XMLReader' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\XmlReaderCaster', 'castXmlReader'],

        'ErrorException' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castErrorException'],
        'Exception' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castException'],
        'Error' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castError'],
        'BeyondSEODeps\Symfony\Bridge\Monolog\Logger' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'BeyondSEODeps\Symfony\Component\DependencyInjection\ContainerInterface' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'BeyondSEODeps\Symfony\Component\EventDispatcher\EventDispatcherInterface' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'BeyondSEODeps\Symfony\Component\HttpClient\AmpHttpClient' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'BeyondSEODeps\Symfony\Component\HttpClient\CurlHttpClient' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'BeyondSEODeps\Symfony\Component\HttpClient\NativeHttpClient' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'BeyondSEODeps\Symfony\Component\HttpClient\Response\AmpResponse' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'BeyondSEODeps\Symfony\Component\HttpClient\Response\CurlResponse' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'BeyondSEODeps\Symfony\Component\HttpClient\Response\NativeResponse' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'BeyondSEODeps\Symfony\Component\HttpFoundation\Request' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castRequest'],
        'Symfony\Component\Uid\Ulid' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUlid'],
        'Symfony\Component\Uid\Uuid' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUuid'],
        'BeyondSEODeps\Symfony\Component\VarDumper\Exception\ThrowingCasterException' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castThrowingCasterException'],
        'BeyondSEODeps\Symfony\Component\VarDumper\Caster\TraceStub' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castTraceStub'],
        'BeyondSEODeps\Symfony\Component\VarDumper\Caster\FrameStub' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castFrameStub'],
        'BeyondSEODeps\Symfony\Component\VarDumper\Cloner\AbstractCloner' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'BeyondSEODeps\Symfony\Component\ErrorHandler\Exception\SilencedErrorContext' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castSilencedErrorContext'],

        'Imagine\Image\ImageInterface' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ImagineCaster', 'castImage'],

        'Ramsey\Uuid\UuidInterface' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\UuidCaster', 'castRamseyUuid'],

        'BeyondSEODeps\ProxyManager\Proxy\ProxyInterface' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ProxyManagerCaster', 'castProxy'],
        'PHPUnit_Framework_MockObject_MockObject' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'PHPUnit\Framework\MockObject\MockObject' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'PHPUnit\Framework\MockObject\Stub' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Prophecy\Prophecy\ProphecySubjectInterface' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Mockery\MockInterface' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'],

        'PDO' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdo'],
        'PDOStatement' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdoStatement'],

        'AMQPConnection' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castConnection'],
        'AMQPChannel' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castChannel'],
        'AMQPQueue' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castQueue'],
        'AMQPExchange' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castExchange'],
        'AMQPEnvelope' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castEnvelope'],

        'ArrayObject' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayObject'],
        'ArrayIterator' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayIterator'],
        'SplDoublyLinkedList' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castDoublyLinkedList'],
        'SplFileInfo' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castFileInfo'],
        'SplFileObject' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castFileObject'],
        'SplHeap' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'],
        'SplObjectStorage' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castObjectStorage'],
        'SplPriorityQueue' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'],
        'OuterIterator' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castOuterIterator'],
        'WeakReference' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\SplCaster', 'castWeakReference'],

        'Redis' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedis'],
        'RedisArray' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisArray'],
        'RedisCluster' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisCluster'],

        'DateTimeInterface' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DateCaster', 'castDateTime'],
        'DateInterval' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DateCaster', 'castInterval'],
        'DateTimeZone' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DateCaster', 'castTimeZone'],
        'DatePeriod' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DateCaster', 'castPeriod'],

        'GMP' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\GmpCaster', 'castGmp'],

        'MessageFormatter' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castMessageFormatter'],
        'NumberFormatter' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castNumberFormatter'],
        'IntlTimeZone' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlTimeZone'],
        'IntlCalendar' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlCalendar'],
        'IntlDateFormatter' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlDateFormatter'],

        'Memcached' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\MemcachedCaster', 'castMemcached'],

        'BeyondSEODeps\Ds\Collection' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DsCaster', 'castCollection'],
        'BeyondSEODeps\Ds\Map' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DsCaster', 'castMap'],
        'BeyondSEODeps\Ds\Pair' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DsCaster', 'castPair'],
        'BeyondSEODeps\Symfony\Component\VarDumper\Caster\DsPairStub' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\DsCaster', 'castPairStub'],

        'mysqli_driver' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\MysqliCaster', 'castMysqliDriver'],

        'CurlHandle' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castCurl'],

        ':dba' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'],
        ':dba persistent' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'],

        'GdImage' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castGd'],
        ':gd' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castGd'],

        ':mysql link' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castMysqlLink'],
        ':pgsql large object' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLargeObject'],
        ':pgsql link' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLink'],
        ':pgsql link persistent' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLink'],
        ':pgsql result' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castResult'],
        ':process' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castProcess'],
        ':stream' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'],

        'OpenSSLCertificate' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castOpensslX509'],
        ':OpenSSL X.509' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castOpensslX509'],

        ':persistent stream' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'],
        ':stream-context' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStreamContext'],

        'XmlParser' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\XmlResourceCaster', 'castXml'],
        ':xml' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\XmlResourceCaster', 'castXml'],

        'BeyondSEODeps\RdKafka' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castRdKafka'],
        'BeyondSEODeps\RdKafka\Conf' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castConf'],
        'BeyondSEODeps\RdKafka\KafkaConsumer' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castKafkaConsumer'],
        'BeyondSEODeps\RdKafka\Metadata\Broker' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castBrokerMetadata'],
        'BeyondSEODeps\RdKafka\Metadata\Collection' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castCollectionMetadata'],
        'BeyondSEODeps\RdKafka\Metadata\Partition' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castPartitionMetadata'],
        'BeyondSEODeps\RdKafka\Metadata\Topic' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicMetadata'],
        'BeyondSEODeps\RdKafka\Message' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castMessage'],
        'BeyondSEODeps\RdKafka\Topic' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopic'],
        'BeyondSEODeps\RdKafka\TopicPartition' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicPartition'],
        'BeyondSEODeps\RdKafka\TopicConf' => ['BeyondSEODeps\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicConf'],
    ];

    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $minDepth = 1;

    /**
     * @var array<string, list<callable>>
     */
    private array $casters = [];

    /**
     * @var callable|null
     */
    private $prevErrorHandler;

    private array $classInfo = [];
    private int $filter = 0;

    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(?array $casters = null)
    {
        if (null === $casters) {
            $casters = static::$defaultCasters;
        }
        $this->addCasters($casters);
    }

    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     */
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }

    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     */
    public function setMaxItems(int $maxItems)
    {
        $this->maxItems = $maxItems;
    }

    /**
     * Sets the maximum cloned length for strings.
     */
    public function setMaxString(int $maxString)
    {
        $this->maxString = $maxString;
    }

    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     */
    public function setMinDepth(int $minDepth)
    {
        $this->minDepth = $minDepth;
    }

    /**
     * Clones a PHP variable.
     *
     * @param int $filter A bit field of Caster::EXCLUDE_* constants
     */
    public function cloneVar(mixed $var, int $filter = 0): Data
    {
        $this->prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }

            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }

            return false;
        });
        $this->filter = $filter;

        if ($gc = gc_enabled()) {
            gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                gc_enable();
            }
            restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }

    /**
     * Effectively clones the PHP variable.
     */
    abstract protected function doClone(mixed $var): array;

    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castObject(Stub $stub, bool $isNested): array
    {
        $obj = $stub->value;
        $class = $stub->class;

        if (str_contains($class, "@anonymous\0")) {
            $stub->class = get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = method_exists($class, '__debugInfo');

            foreach (class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';

            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : [
                'file' => $r->getFileName(),
                'line' => $r->getStartLine(),
            ];

            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }

        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);

        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '').'⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }

    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castResource(Stub $stub, bool $isNested): array
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;

        try {
            if (!empty($this->casters[':'.$type])) {
                foreach ($this->casters[':'.$type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '').'⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }
}
