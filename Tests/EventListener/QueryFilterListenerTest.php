<?php declare(strict_types=1);

namespace Tests\Artprima\QueryFilterBundle\Request;

use Artprima\QueryFilterBundle\EventListener\QueryFilterListener;
use Artprima\QueryFilterBundle\QueryFilter\Config\ConfigInterface;
use Artprima\QueryFilterBundle\QueryFilter\QueryFilter;
use Artprima\QueryFilterBundle\Response\ResponseInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class QueryFilterListenerTest extends TestCase
{
    public function testOnKernelView()
    {
        $response = self::getMockBuilder(ResponseInterface::class)
            ->getMock();

        $config = self::getMockBuilder(ConfigInterface::class)
            ->getMock();

        $queryFilter = self::getMockBuilder(QueryFilter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $queryFilter
            ->expects(self::once())
            ->method('getData')
            ->with($config)
            ->willReturn($response);

        $event = $this->getGetResponseForControllerResultEvent($config, $this->createRequest(
            new \Artprima\QueryFilterBundle\Controller\Annotations\QueryFilter([])
        ));

        $listener = new QueryFilterListener($queryFilter);
        $listener->onKernelView($event);

        self::assertEquals($response, $event->getControllerResult());
    }

    /**
     * @test
     */
    public function onKernelView_should_do_nothing_on_queryfilter_attribute_not_set()
    {
        $response = self::getMockBuilder(ResponseInterface::class)
            ->getMock();

        $config = self::getMockBuilder(ConfigInterface::class)
            ->getMock();

        $queryFilter = self::getMockBuilder(QueryFilter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $queryFilter
            ->expects(self::never())
            ->method('getData')
            ->with($config)
            ->willReturn($response);

        $event = $this->getGetResponseForControllerResultEvent($config, $this->createRequest());

        $listener = new QueryFilterListener($queryFilter);
        $listener->onKernelView($event);

        self::assertEquals($config, $event->getControllerResult());
    }

    /**
     * @test
     */
    public function onKernelView_should_do_nothing_on_controller_result_not_instance_of_ConfigInterface()
    {
        $response = self::getMockBuilder(ResponseInterface::class)
            ->getMock();

        $config = 'idiocracy';

        $queryFilter = self::getMockBuilder(QueryFilter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $queryFilter
            ->expects(self::never())
            ->method('getData')
            ->with($config)
            ->willReturn($response);

        $event = $this->getGetResponseForControllerResultEvent($config, $this->createRequest());

        $listener = new QueryFilterListener($queryFilter);
        $listener->onKernelView($event);

        self::assertEquals($config, $event->getControllerResult());
    }

    private function getGetResponseForControllerResultEvent($config, Request $request)
    {
        $mockKernel = $this->getMockForAbstractClass('Symfony\Component\HttpKernel\Kernel', ['', '']);

        return new GetResponseForControllerResultEvent($mockKernel, $request, HttpKernelInterface::MASTER_REQUEST, $config);
    }

    private function createRequest($queryFilterAnnotation = null)
    {
        return new Request([], [], [
            '_queryfilter' => $queryFilterAnnotation,
        ]);
    }}
