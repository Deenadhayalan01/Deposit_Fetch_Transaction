-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 14, 2026 at 05:43 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `deposit_fetch`
--

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `source` text DEFAULT NULL,
  `basecoin` varchar(100) NOT NULL DEFAULT '',
  `basetoken` varchar(100) NOT NULL DEFAULT '',
  `withdraw` double DEFAULT NULL,
  `withdraw_type` varchar(50) NOT NULL DEFAULT '',
  `buy_trade` double DEFAULT NULL,
  `sell_trade` double DEFAULT NULL,
  `maker_trade` double NOT NULL DEFAULT 0,
  `taker_trade` double NOT NULL DEFAULT 0,
  `affiliate_commission` double NOT NULL DEFAULT 0,
  `affiliate_commission_status` double NOT NULL DEFAULT 0,
  `type` enum('fiat','coin','token','stock','bep20token') NOT NULL,
  `coinname` varchar(191) DEFAULT NULL,
  `netfee` double DEFAULT NULL,
  `point_value` int(11) DEFAULT NULL,
  `min_deposit` double DEFAULT NULL,
  `max_deposit` double NOT NULL DEFAULT 0,
  `min_withdraw` double DEFAULT NULL,
  `max_withdraw` double DEFAULT NULL,
  `perday_withdraw` double DEFAULT NULL,
  `min_trade_price` double DEFAULT NULL,
  `url` text DEFAULT NULL,
  `contractaddress` text DEFAULT NULL,
  `abiarray` text DEFAULT NULL,
  `decimal` double DEFAULT 8,
  `image` text DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `shown` tinyint(4) DEFAULT NULL,
  `orderlist` tinyint(4) DEFAULT NULL,
  `ngn_conv_price` double NOT NULL DEFAULT 0,
  `withdrw_ngn_conv_price` double DEFAULT 0,
  `is_own_token` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `actual_liveprice` double DEFAULT NULL,
  `usdliveprice` double NOT NULL DEFAULT 0,
  `network_fee` double NOT NULL DEFAULT 0,
  `actual_coin_name` varchar(200) DEFAULT NULL,
  `p2p_maker_fee` double DEFAULT 0,
  `p2p_taker_fee` double DEFAULT 0,
  `vip_maker_trade` double DEFAULT 0,
  `vip_taker_trade` double DEFAULT 0,
  `vip_settings_status` varchar(10) DEFAULT 'off'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commissions`
--

INSERT INTO `commissions` (`id`, `source`, `basecoin`, `basetoken`, `withdraw`, `withdraw_type`, `buy_trade`, `sell_trade`, `maker_trade`, `taker_trade`, `affiliate_commission`, `affiliate_commission_status`, `type`, `coinname`, `netfee`, `point_value`, `min_deposit`, `max_deposit`, `min_withdraw`, `max_withdraw`, `perday_withdraw`, `min_trade_price`, `url`, `contractaddress`, `abiarray`, `decimal`, `image`, `status`, `shown`, `orderlist`, `ngn_conv_price`, `withdrw_ngn_conv_price`, `is_own_token`, `created_at`, `updated_at`, `actual_liveprice`, `usdliveprice`, `network_fee`, `actual_coin_name`, `p2p_maker_fee`, `p2p_taker_fee`, `vip_maker_trade`, `vip_taker_trade`, `vip_settings_status`) VALUES
(1, 'BTC', '', '', 5, '', 1, 1, 1, 1, 0.2, 1, 'coin', 'BITCOIN', 0, 8, 0, 0, 0, 0, 10000000, 0, 'https://www.blockchain.com/btc/address/', '', '', 8, 'btc.svg', 1, 1, 1, 0, 0, 0, '2020-03-26 12:03:22', '2025-03-11 03:41:10', 0, 0, 5, 'BTC', 0, 0, 0, 0, 'off'),
(2, 'ETH', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'coin', 'ETHEREUM', 0, 8, 0, 0, 0, 0, 100000, 0.0001, 'https://etherscan.io/address/', '', '', 8, 'eth.svg', 1, 1, 1, 0, 0, 0, '2020-03-26 12:03:22', '2024-04-18 07:05:03', 2971.81, 2971.81, 0, 'ETH', 0, 0, 0, 0, 'off'),
(3, 'LTC', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'coin', 'LITECOIN', 0, 8, 0, 0, 0, 0, 10000000, 0, 'https://live.blockcypher.com/ltc/address/', NULL, NULL, 8, 'ltc.svg', 1, 1, 1, 0, 0, 0, '2020-03-26 12:03:22', '2024-04-18 07:05:04', 79.29, 79.29, 0.0451949, 'LTC', 0, 0, 0, 0, 'off'),
(4, 'USDT', 'ETH', 'ERC20', 0.2, 'percentage', 1.5, 1, 0.6, 0.6, 0.5, 1, 'token', 'TETHER', 0, 5, 1, 0, 1, 10000000, 1000000, 0.000001, 'https://etherscan.io/address/', '0xdAC17F958D2ee523a2206206994597C13D831ec7', NULL, 5, 'usdt.svg', 1, 1, 1, 1189, 1090, 0, '2020-03-26 12:03:22', '2024-04-18 06:14:01', 0.999911, 0.999911, 0, 'USD', 0, 0, 0, 0, 'off'),
(8, 'XRP', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'coin', 'RIPPLE', 0, 8, 1, 0, 0, 0, 100000, 0.1, 'https://xrpscan.com/account/', NULL, NULL, 8, 'xrp.svg', 1, 1, 1, 0, 0, 0, '2021-05-05 07:35:42', '2024-04-18 07:05:06', 0.4894, 0.4894, 0.00045683, 'XRP', 0, 0, 0, 0, 'off'),
(9, 'BNB', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'coin', 'BINANCECOIN', 0, 8, 0, 0, 0, 0, 1000000, 0, 'https://bscscan.com/address/', NULL, NULL, 8, 'bnb.svg', 1, 1, 1, 0, 0, 0, '2021-09-03 07:04:49', '2024-04-18 07:05:07', 543.4, 543.4, 0, 'BNB', 0, 0, 0, 0, 'off'),
(11, 'SHIB', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'token', 'Shiba-Inu', 0, NULL, 0, 0, 1, 0, 0, 0.00001, 'https://shibatoken.com', '0x95aD61b0a150d79219dCF64E1E6Cc01f0B64C4cE', NULL, 18, 'shib.svg', 1, NULL, NULL, 0, 0, 0, '2022-05-24 07:10:08', '2024-04-18 07:05:08', 0.00002174, 0.00002174, 0, '', 0, 0, 0, 0, 'off'),
(12, 'CHR', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'token', 'CHROMAWAY', 0, NULL, 1, 0, 1, 0, 0, 0, 'https://chromia.com/', '0x8A2279d4A90B6fe1C4B30fa660cC9f926797bAA2', NULL, 6, 'chr.svg', 0, NULL, NULL, 0, 0, 0, '2022-05-29 09:43:40', '2024-04-18 07:05:09', 0.2955, 0.2955, 0, 'chromaway', 0, 0, 0, 0, 'off'),
(13, 'DENT', '', '', 6, '', 2, 2, 1.5, 1.5, 1.5, 1, 'token', 'DENT', 0, NULL, 1, 0, 1, 0, 0, 0, 'http://www.dentcoin.com', '0x3597bfD533a99c9aa083587B074434E61Eb0A258', NULL, 8, 'dent.svg', 0, NULL, NULL, 0, 0, 0, '2022-06-02 02:50:07', '2024-04-18 07:05:10', 0.001243, 0.001243, 0, 'DENT', 0, 0, 0, 0, 'off'),
(14, 'BADGER', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'token', 'BADGER-DAO', 0, NULL, 0, 0, 5, 0, 0, 0.1, 'https://badger.finance/', '0x3472A5A71965499acd81997a54BBA8D852C6E53d', NULL, 18, 'badger.svg', 0, NULL, NULL, 0, 0, 0, '2022-06-09 03:09:44', '2024-04-18 07:05:11', 4.124, 4.124, 0, NULL, 0, 0, 0, 0, 'off'),
(15, 'GALA', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'token', 'GALA', 0, NULL, 0, 0, 0, 0, 0, 0.0001, 'https://gala.games', '0x15D4c048F83bd7e37d49eA4C83a07267Ec4203dA', NULL, 8, 'gala.svg', 1, NULL, NULL, 0, 0, 0, '2022-06-10 17:34:55', '2024-04-18 07:05:12', 0.04006, 0.04006, 0, NULL, 0, 0, 0, 0, 'off'),
(16, 'ANT', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'token', 'ARAGON', 0, NULL, 0, 0, 1, 0, 0, 0.001, 'https://aragon.org/', '0xa117000000f279D81A1D3cc75430fAA017FA5A2e', NULL, 18, 'ant.svg', 0, NULL, NULL, 0, 0, 0, '2022-06-11 18:00:30', '2024-02-29 07:50:27', 0.1377, 0.1377, 0, NULL, 0, 0, 0, 0, 'off'),
(17, 'REEF', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'token', 'REEF', 0, NULL, 0, 0, 0, 0, 0, 0, 'https://reef.finance/', '0xFE3E6a25e6b192A42a44ecDDCd13796471735ACf', NULL, 18, 'reef.svg', 0, NULL, NULL, 0, 0, 0, '2022-06-20 03:44:21', '2024-04-18 07:05:14', 0.001925, 0.001925, 0, NULL, 0, 0, 0, 0, 'off'),
(18, 'APE', '', '', 5, '', 2, 2, 1.5, 1.5, 0.2, 1, 'token', 'APECOIN', 0, NULL, 0, 0, 0, 0, 0, 0.001, 'https://apecoin.com/', '0x4d224452801ACEd8B2F0aebE155379bb5D594381', NULL, 18, 'ape.svg', 1, NULL, NULL, 0, 0, 0, '2022-06-20 04:01:47', '2024-04-18 07:05:15', 1.184, 1.184, 0, NULL, 0, 0, 0, 0, 'off'),
(19, 'ALPHA', '', '', 5, '', 2, 2, 1.5, 1.5, 0.5, 1, 'token', 'ALPHA-FINANCE', 0, NULL, 0, 0, 1, 0, 0, 0, 'https://alphafinance.io/', '0xa1faa113cbE53436Df28FF0aEe54275c13B40975', NULL, 18, 'alpha.svg', 0, NULL, NULL, 0, 0, 0, '2022-06-20 04:10:35', '2024-04-18 07:05:16', 0.1102, 0.1102, 0, NULL, 0, 0, 0, 0, 'off'),
(20, 'CHZ', '', '', 5, '', 2, 2, 1.5, 1.5, 0.5, 1, 'token', 'CHILIZ', 0, NULL, 0, 0, 1, 0, 0, 0, 'https://www.chiliz.com/', '0x3506424F91fD33084466F402d5D97f05F8e3b4AF', NULL, 18, 'chz.svg', 1, NULL, NULL, 0, 0, 0, '2022-06-20 04:14:20', '2024-04-18 07:05:17', 0.10876, 0.10876, 0, NULL, 0, 0, 0, 0, 'off'),
(21, '1INCH', '', '', 5, '', 2, 2, 1.5, 1.5, 0.5, 1, 'token', '1INCH', 0, NULL, 0, 0, 0.1, 0, 0, 0, 'https://1inch.io/', '0x111111111117dC0aa78b770fA6A738034120C302', NULL, 18, '1inch.svg', 1, NULL, NULL, 0, 0, 0, '2022-06-20 04:16:58', '2024-04-18 07:05:18', 0.3789, 0.3789, 0, NULL, 0, 0, 0, 0, 'off'),
(22, 'AAVE', '', '', 5, '', 2, 2, 1.5, 1.5, 0.5, 1, 'token', 'AAVE', 0, NULL, 0, 0, 0.00001, 0, 0, 0, '1', '0x7Fc66500c84A76Ad7e9c93437bFc5Ac33E2DDaE9', NULL, 18, 'aave.svg', 1, NULL, NULL, 0, 0, 0, '2022-06-20 04:23:08', '2024-04-18 07:05:19', 83.09, 83.09, 0, NULL, 0, 0, 0, 0, 'off'),
(23, 'SLP', '', '', 5, '', 0, 0, 1.5, 1.5, 0.5, 1, 'token', 'SMOOTH-LOVE-POTION', 0, NULL, 0, 0, 1, 0, 0, 0, 'https://axieinfinity.com/', '0xCC8Fa225D80b9c7D42F96e9570156c65D6cAAa25', NULL, 0, 'slp.svg', 1, NULL, NULL, 0, 0, 0, '2022-06-22 11:33:58', '2024-04-18 07:05:20', 0.003779, 0.003779, 0, NULL, 0, 0, 0, 0, 'off'),
(24, 'UNI', '', '', 5, '', 0, 0, 1.5, 1.5, 0.5, 1, 'token', 'UNISWAP', 0, NULL, 0.0001, 0, 0.0001, 0, 0, 0, 'https://uniswap.org/', '0x1f9840a85d5aF5bf1D1762F925BDADdC4201F984', NULL, 18, 'uni.svg', 1, NULL, NULL, 0, 0, 0, '2022-07-09 14:31:07', '2024-04-18 07:05:21', 6.895, 6.895, 0, NULL, 0, 0, 0, 0, 'off'),
(25, 'COTI', '', '', 5, '', 0, 0, 0, 0, 0, 0, 'token', 'COTI', NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, 'https://coti.io/', '0xDDB3422497E61e13543BeA06989C0789117555c5', NULL, 18, 'coti.svg', 1, NULL, NULL, 0, 0, 0, '2022-08-09 04:19:44', '2024-04-18 07:05:22', 0.10509, 0.10509, 0, NULL, 0, 0, 0, 0, 'off'),
(27, 'SVT', '', '', 0, '', 0, 0, 0.5, 0.5, 1, 1, 'token', 'Savita Coin', NULL, NULL, 1, 0, 0, 0, 0, 0, 'test', 'Test', NULL, 8, 'savita coin.svg', 1, NULL, NULL, 0, 0, 1, '2022-08-19 09:34:12', '2022-08-19 09:48:05', NULL, 0.5, 0, NULL, 0, 0, 0, 0, 'off'),
(29, 'ENS', '', '', 5, '', 0, 0, 1.5, 1.5, 1, 1, 'token', 'ETHEREUM-NAME-SERVICE', NULL, NULL, 0, 10000000, 1, 0, 0, 0, 'https://ens.domains/', '0xC18360217D8F7Ab5e7c516566761Ea12Ce7F9D72', NULL, 18, 'ens.svg', 1, NULL, NULL, 0, 0, 0, '2023-02-28 07:03:33', '2024-04-18 07:05:23', 13.14, 13.14, 0, NULL, 0, 0, 0, 0, 'off'),
(30, 'BAT', '', '', 5, '', 0, 0, 1.5, 1.5, 1, 1, 'token', 'BASIC-ATTENTION-TOKEN', NULL, NULL, 1, 10000000, 1, 0, 0, 0, 'https://basicattentiontoken.org/', '0x0D8775F648430679A709E98d2b0Cb6250d2887EF', NULL, 18, 'bat.svg', 1, NULL, NULL, 0, 0, 0, '2023-02-28 07:05:42', '2024-04-18 07:05:24', 0.2331, 0.2331, 0, NULL, 0, 0, 0, 0, 'off'),
(35, 'TRX', '', '', 5, '', 2, 2, 1.5, 1.5, 1, 1, 'coin', 'TRXCOIN', 0, 8, NULL, 0, 0, 0, 10000000, 0, 'https://tronscan.org/#/address/', NULL, NULL, 8, 'trx.svg', 1, 1, 1, 0, 0, 0, '2023-03-02 12:03:22', '2024-04-18 07:05:25', 0.1091, 0.1091, 0.0592127, 'TRX', 0, 0, 0, 0, 'off'),
(36, 'WIN', '', '', 5, '', NULL, NULL, 1.5, 1.5, 0.5, 1, 'token', 'WinkLink (WIN)', NULL, NULL, 1, 0, 1, 0, 0, 0, 'https://winklink.org/', NULL, NULL, 8, 'win.svg', 0, NULL, NULL, 0, 0, 0, '2023-03-06 10:42:10', '2024-04-18 07:05:26', 0.0001176, 0.0001176, 0, NULL, 0, 0, 0, 0, 'off'),
(37, 'MANA', '', '', 5, '', 0, 0, 1.5, 1.5, 1, 1, 'token', 'DECENTRALAND', NULL, NULL, 1, 0, 1, 0, 0, 0, 'https://decentraland.org/', NULL, NULL, 8, 'mana.svg', 1, NULL, NULL, 0, 0, 0, '2023-03-09 06:06:08', '2024-04-18 07:05:27', 0.419, 0.419, 0, NULL, 0, 0, 0, 0, 'off'),
(38, 'SAND', '', '', 5, '', 0, 0, 1.5, 1.5, 0, 1, 'token', 'SAN-DIEGO-COIN', NULL, NULL, 1, 0, 1, 0, 0, 0, 'http://www.sandbox.game/', NULL, NULL, 8, 'sand.svg', 1, NULL, NULL, 0, 0, 0, '2023-03-09 06:07:56', '2024-04-18 07:05:28', 0.4227, 0.4227, 0, NULL, 0, 0, 0, 0, 'off'),
(39, 'IOTX', '', '', 5, '', 0, 0, 1.5, 1.5, 1, 1, 'token', 'BINANCE-PEG-IOTEX', NULL, NULL, 1, 0, 1, 0, 0, 0, 'https://iotex.io/', NULL, NULL, 8, 'iotx.svg', 0, NULL, NULL, 0, 0, 0, '2023-03-09 06:15:52', '2024-04-18 07:05:29', 0.05062, 0.05062, 0, NULL, 0, 0, 0, 0, 'off'),
(40, 'GRT', '', '', 5, '', 0, 0, 1.5, 1.5, 1, 1, 'token', 'GOLDEN-RATIO-TOKEN', NULL, NULL, 1, 0, 1, 0, 0, 0, 'https://thegraph.com/', NULL, NULL, 8, 'grt.svg', 1, NULL, NULL, 0, 0, 0, '2023-03-09 06:19:56', '2024-04-18 07:05:30', 0.2376, 0.2376, 0, NULL, 0, 0, 0, 0, 'off'),
(41, 'ID', '', '', 1, '', NULL, NULL, 1.5, 1.5, 0, 0, 'token', 'SPACE ID', NULL, NULL, 1, 0, 1, 0, 0, 0, 'https://space.id/', NULL, NULL, 8, 'id.svg', 1, NULL, NULL, 0, 0, 0, '2023-04-20 02:54:42', '2024-04-18 07:05:31', 0.69736, 0.69736, 0, NULL, 0, 0, 0, 0, 'off'),
(42, 'SOL', '', '', 5, '', 1, 1, 1, 1, 0.2, 1, 'coin', 'SOLANA', 0, 8, 0, 0, 0, 0, 10000000, 0, 'https://solscan.io/account/', '', '', 8, 'sol.svg', 1, 1, 1, 0, 0, 0, '2024-03-25 12:03:22', '2024-04-18 07:05:32', 130.56, 130.56, 5, 'SOL', 0, 0, 0, 0, 'off'),
(43, 'BCH', '', '', 5, '', 1, 1, 1, 1, 0.2, 1, 'coin', 'Bitcoin cash', 0, 8, 0, 0, 0, 0, 10000000, 0, 'https://blockchair.com/bitcoin-cash/address/', '', '', 8, 'bch.svg', 1, 1, 1, 0, 0, 0, '2024-03-30 12:03:22', '2024-04-18 07:05:33', 465.3, 465.3, 5, 'BCH', 0, 0, 0, 0, 'off');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
