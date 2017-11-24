<?php

namespace Shreeji\UrlRewriteGenerator\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Store\Model\Store;

class UrlGenerateProduct extends Command {

    /**
     * @var ProductUrlRewriteGenerator
     */
    protected $productUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @var ProductRepositoryInterface
     */
    protected $collection;

    /**
     * 
     * @param \Magento\Framework\App\State $state
     * @param Collection $collection
     * @param ProductUrlRewriteGenerator $productUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(
    \Magento\Framework\App\State $state, Collection $collection, ProductUrlRewriteGenerator $productUrlRewriteGenerator, UrlPersistInterface $urlPersist
    ) {        
        $this->collection = $collection;
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        parent::__construct();
    }

    protected function configure() {
        $this->setName('si:url-regenerate-product')
                ->setDescription('URL Rewrite Generator For Product')
                ->addArgument(
                        'p_ids', InputArgument::IS_ARRAY, 'URL Rewrite Generate For Specific Product Ids'
                )
                ->addOption(
                        'store', '', InputOption::VALUE_REQUIRED, 'URL Rewrite Generate For Specific Store', Store::DEFAULT_STORE_ID
        );
    }

    /**
     * Main login behind URL Rewrite Generator
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('Starting URL Rewrite Generator For Product');
        $store_id = $input->getOption('store');
        $this->collection->addStoreFilter($store_id)->setStoreId($store_id);
        $pids = $input->getArgument('p_ids');
        if (!empty($pids)){
            $this->collection->addIdFilter($pids);
        }            
        $this->collection->addAttributeToSelect(['url_path', 'url_key']);
        $list = $this->collection->load();
        $count = 0;
        foreach ($list as $product) {            
            $product->setStoreId($store_id);
            $this->urlPersist->deleteByData([
                UrlRewrite::ENTITY_ID => $product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                UrlRewrite::REDIRECT_TYPE => 0,
                UrlRewrite::STORE_ID => $store_id
            ]);
            try {
                $this->urlPersist->replace(
                        $this->productUrlRewriteGenerator->generate($product)
                );
                $output->write('-');                                    
                $count++;
            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }
        $output->writeln('<info>Url Rewrite Successfully generated.</info>');
    }

}
