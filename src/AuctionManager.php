<?php

namespace Dzheyden8561\AuctionsHouse;

use JsonException;
use Dzheyden8561\AuctionsHouse\data\Auction;
use libs\NhanAZ\libBedrock\libBedrockException;
use pocketmine\item\Item;
use pocketmine\utils\Config;

class AuctionManager {

    private array $auctions = [];
    private Config $config;

    /**
     * @throws libBedrockException
     */
    public function __construct(Config $config) {
        $this->config = $config;
        $this->loadAuctions();
    }

    /**
     * @throws JsonException
     */
    public function createAuction(string $seller, Item $item, int $startingBid, ?int $buyNowPrice, int $duration): void {
        $auction = new Auction($seller, $item, $startingBid, $buyNowPrice, $duration);
        $this->auctions[] = $auction;
        $this->saveAuctions();
    }

    public function getAuctionsByCategory(string $category): array {
        // Implement filtering logic based on item categories.
        return $this->auctions;
    }

    public function getAuctions(): array {
        return $this->auctions;
    }

    public function removeAuction(Auction $auction): void {
        $index = array_search($auction, $this->auctions, true);
        if ($index !== false) {
            unset($this->auctions[$index]);
            $this->auctions = array_values($this->auctions);
            $this->saveAuctions();
        }
    }

    /**
     * @throws libBedrockException
     */
    private function loadAuctions(): void {
        $this->auctions = [];
        $data = $this->config->get("auctions", []);
        foreach ($data as $auctionData) {
            $auction = Auction::deserialize($auctionData);
            $this->auctions[] = $auction;
        }
    }

    /**
     * @throws JsonException
     */
    public function saveAuctions(): void {
        $data = [];
        foreach ($this->auctions as $auction) {
            $data[] = $auction->serialize();
        }
        $this->config->set("auctions", $data);
        $this->config->save();
    }
}