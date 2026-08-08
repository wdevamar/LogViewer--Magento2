<?php

namespace WdevAmar\LogViewer\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                if (isset($item["name"])) {
                    $item[$name]["view"] = [
                        "href" => $this->urlBuilder->getUrl(
                            "logviewer/index/view",
                            ["file" => $item["name"]]
                        ),
                        "label" => __("View"),
                        "hidden" => false,
                    ];
                    $item[$name]["download"] = [
                        "href" => $this->urlBuilder->getUrl(
                            "logviewer/index/download",
                            ["file" => $item["name"]]
                        ),
                        "label" => __("Download"),
                        "hidden" => false,
                    ];
                    $item[$name]["delete"] = [
                        "href" => $this->urlBuilder->getUrl(
                            "logviewer/index/delete",
                            ["file" => $item["name"]]
                        ),
                        "label" => __("Delete"),
                        "confirm" => [
                            "title" => __("Delete \"%1\"", $item["name"]),
                            "message" => __("Are you sure you want to delete the \"%1\" log file?", $item["name"])
                        ],
                        "post" => true,
                        "hidden" => false,
                    ];
                }
            }
        }
        return $dataSource;
    }
}
