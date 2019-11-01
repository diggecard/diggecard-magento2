<?php

namespace Diggecard\Giftcard\Console;

use Diggecard\Giftcard\Observer\Api\AfterOrderSave\Post\Order\Complete;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;
use mysql_xdevapi\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SomeCommand
 */
class Order extends Command
{
    const MODE_ARGUMENT = 'order-id';

    /** @var OrderFactory */
    protected $order;

    /** @var Complete */
    protected $complete;

    public function __construct(
        OrderFactory $order,
        Complete $complete,
        string $name = null
    )
    {
        $this->order = $order;
        $this->complete = $complete;

        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::MODE_ARGUMENT,
                null,
                InputOption::VALUE_REQUIRED,
                'Enter order id'
            )
        ];

        $this->setName('diggecard:giftcard:order');
        $this->setDescription('');
        $this->setDefinition($options);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($orderIncrementId = (int)$input->getOption(self::MODE_ARGUMENT)){
            $orderDetails = $this->order->create()->loadByIncrementId($orderIncrementId);

            $invoice = $orderDetails->getInvoiceCollection()->getFirstItem();
            if ($invoice->getId()) {
                $event = new DataObject();
                $event->setInvoice($invoice);

                $observer = new Observer();
                $observer->setEvent($event);

                try {
                    $this->complete->execute($observer);
                } catch (\Exception $exception) {
                    echo $exception->getMessage();
                    die;
                }

                echo 'Completed';
            }

            echo 'No invoice';
        } else
            echo 'Please pass the argument --order-id with order id';
    }

}