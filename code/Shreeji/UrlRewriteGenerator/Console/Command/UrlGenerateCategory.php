<?php

namespace Shreeji\UrlRewriteGenerator\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Observer\UrlRewriteHandler;

class UrlGenerateCategory extends Command {

    /**
     * @var CategoryUrlRewriteGenerator
     */
    protected $categoryUrlRewriteGenerator;

    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $collection;

    /**
     *
     * @var state 
     */
    protected $state;
    
    /** @var UrlRewriteHandler */
    protected $urlRewriteHandler;

    /**
     * 
     * @param \Magento\Framework\App\State $state
     * @param Collection $collection
     * @param CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     */
    public function __construct(
    \Magento\Framework\App\State $state, Collection $collection, CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator, UrlPersistInterface $urlPersist,UrlRewriteHandler $urlRewriteHandler
    ) {
        $this->state = $state;
        $this->collection = $collection;
        $this->categoryUrlRewriteGenerator = $categoryUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->urlRewriteHandler = $urlRewriteHandler;    
        $this->state->setAreaCode('adminhtml');
        parent::__construct();
    }

    protected function configure() {
        $this->setName('si:url-regenerate-category')
                ->setDescription('URL Rewrite Generator For Category')
                ->addArgument(
                        'c_ids', InputArgument::IS_ARRAY, 'URL Rewrite Generate For Specific Category Ids'
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
        $output->writeln('Starting URL Rewrite Generator For Category');
        $store_id = $input->getOption('store');
        $this->collection->setStoreId($store_id);
        $pids = $input->getArgument('c_ids');
        if (!empty($pids))
            $this->collection->addIdFilter($pids);
        $this->collection->addAttributeToSelect(['url_path', 'url_key']);
        $list = $this->collection->load();
        $count = 0;
        foreach ($list as $category) {            
             $category->setStoreId($store_id);
            if ($category->getParentId() == Category::TREE_ROOT_ID) {
                continue;
            }
            try {
                $urlRewrites = array_merge(
                        $this->categoryUrlRewriteGenerator->generate($category, true), $this->urlRewriteHandler->generateProductUrlRewrites($category)
                );                
                $this->urlPersist->replace($urlRewrites);                
                $output->write('-');
                $count++;
            } catch (\Exception $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }
        $output->writeln('<info>Url Rewrite Successfully generated.</info>');
    }

}
