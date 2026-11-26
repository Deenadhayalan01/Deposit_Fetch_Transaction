--
-- Database: `deposit_fetch`
--

-- --------------------------------------------------------

--
-- Table structure for table `cryptotransactions`
--

CREATE TABLE `cryptotransactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uid` bigint(20) UNSIGNED DEFAULT NULL,
  `fuid` bigint(20) DEFAULT NULL,
  `tuid` bigint(20) DEFAULT NULL,
  `currency` varchar(191) DEFAULT NULL,
  `txtype` varchar(191) DEFAULT NULL,
  `txid` varchar(191) DEFAULT NULL,
  `fiat_trans_id` bigint(20) NOT NULL DEFAULT 0,
  `from_addr` varchar(191) DEFAULT NULL,
  `to_addr` varchar(191) DEFAULT NULL,
  `dest_tag` varchar(20) DEFAULT '',
  `amount` double DEFAULT NULL,
  `confirmation` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `feestatus` int(11) NOT NULL DEFAULT 1,
  `nirvaki_nilai` int(11) NOT NULL DEFAULT 0,
  `usdt_deposit_type` varchar(191) DEFAULT NULL,
  `netfee` double DEFAULT NULL,
  `adminfee` double DEFAULT NULL,
  `totalamount` double NOT NULL DEFAULT 0,
  `gasprice` double NOT NULL DEFAULT 0,
  `is_ngn_convert` tinyint(4) NOT NULL DEFAULT 0,
  `usdt_ngn_conv_amt` double NOT NULL DEFAULT 0,
  `ngn_conv_price` double DEFAULT 0,
  `bank_ref_id` varchar(200) DEFAULT '',
  `multinet_token_symbol` varchar(200) DEFAULT NULL,
  `coldwallettxid` varchar(200) NOT NULL DEFAULT '',
  `multinet_token_network` varchar(200) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_bep20token_address`
--

CREATE TABLE `user_bep20token_address` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `address` text NOT NULL,
  `narcanru` text NOT NULL,
  `balance` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `walletdetail`
--

CREATE TABLE `walletdetail` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uid` bigint(20) UNSIGNED NOT NULL,
  `refid` text DEFAULT NULL,
  `refentry` varchar(200) DEFAULT '',
  `refentrysubtype` varchar(100) NOT NULL DEFAULT '',
  `currency` varchar(30) NOT NULL DEFAULT '',
  `currencytype` varchar(30) DEFAULT '',
  `volumein` double NOT NULL DEFAULT 0,
  `volumeout` double NOT NULL DEFAULT 0,
  `balance` double NOT NULL DEFAULT 0,
  `ActualValue` double NOT NULL DEFAULT 0,
  `fee` double NOT NULL DEFAULT 0,
  `feepercent` double(30,8) NOT NULL DEFAULT 0.00000000,
  `remarks` varchar(200) DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `docdate_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uid` bigint(20) UNSIGNED NOT NULL,
  `currency` varchar(191) DEFAULT NULL,
  `mukavari` text DEFAULT NULL,
  `payment_id` text DEFAULT NULL,
  `balance` decimal(30,8) NOT NULL DEFAULT 0.00000000,
  `escrow_balance` decimal(30,8) NOT NULL DEFAULT 0.00000000,
  `site_balance` decimal(30,8) NOT NULL DEFAULT 0.00000000,
  `receive` decimal(30,8) NOT NULL DEFAULT 0.00000000,
  `spend` decimal(30,8) NOT NULL DEFAULT 0.00000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cryptotransactions`
--
ALTER TABLE `cryptotransactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `txid` (`txid`),
  ADD KEY `cryptotransactions_uid_foreign` (`uid`);

--
-- Indexes for table `user_bep20token_address`
--
ALTER TABLE `user_bep20token_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_bnb_addresses`
--
ALTER TABLE `user_bnb_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_eth_addresses`
--
ALTER TABLE `user_eth_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_token_address`
--
ALTER TABLE `user_token_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `walletdetail`
--
ALTER TABLE `walletdetail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`);

-- --------------------------------------------------------

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cryptotransactions`
--
ALTER TABLE `cryptotransactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_bep20token_address`
--
ALTER TABLE `user_bep20token_address`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2152;

--
-- AUTO_INCREMENT for table `user_bnb_addresses`
--
ALTER TABLE `user_bnb_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2155;

--
-- AUTO_INCREMENT for table `user_eth_addresses`
--
ALTER TABLE `user_eth_addresses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2155;

--
-- AUTO_INCREMENT for table `user_token_address`
--
ALTER TABLE `user_token_address`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2155;

--
-- AUTO_INCREMENT for table `walletdetail`
--
ALTER TABLE `walletdetail`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66573;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;