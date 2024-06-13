-- Bennit DB dump

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `bennit`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_roles`
--

CREATE TABLE `tbl_roles` (
  `id` int(11) NOT NULL COMMENT 'role_id',
  `role` varchar(255) DEFAULT NULL COMMENT 'role_text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_roles`
--

INSERT INTO `tbl_roles` (`id`, `role`) VALUES
(1, 'Admin'),
(2, 'Editor'),
(3, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `roleid` tinyint(4) DEFAULT NULL,
  `is_disabled` bit DEFAULT 0,
  `is_firstrun` bit DEFAULT 1,
  `stripe_id` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `fullname`, `username`, `email`, `password`, `phone`, `roleid`, `is_disabled`, `is_firstrun`, `created_at`, `updated_at`) VALUES
(1, 'Kathy Cahalane', 'kathy', 'kathy@bennit.ai', '8bc7fa56ac4a66d2cb0fc781d213c14f2d518e2be0e1fe65e83231514420f011', '4409419536', 1, 0, 0, '2023-08-16 18:20:24', '2020-08-16 18:20:24'),
(2, 'Jonathan Wise', 'jwise', 'jonathan@bennit.ai', '8bc7fa56ac4a66d2cb0fc781d213c14f2d518e2be0e1fe65e83231514420f011', '2167721051', 1, 0, 0, '2023-08-16 16:23:01', '2023-08-16 16:23:01'),
(3, 'Guest User', 'guest', 'guest@bennit.ai', '8016040fc911a0900c62d0da720ff13114f845d6eb84a923bb86537ec5896081', '5551234', 3, 1, 0, '2023-08-06 19:32:27', '2023-08-06 19:32:27');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payments`
--

CREATE TABLE `tbl_subscriptions` (
  `id` int(11) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `fk_user_id` varchar(30) NOT NULL,
  `fk_org_id` varchar(30),
  `subscription_type` varchar(255) NULL,
  `purchase_token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `canceled_at` timestamp DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
--
-- Table structure for table `tbl_solvers`
--

CREATE TABLE `tbl_solvers` (
  `id` varchar(30) NOT NULL,
  `fk_user_id` varchar(30) NOT NULL,
  `fk_org_id` varchar(30) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `abstract` varchar(255) DEFAULT NULL,
  `experience` LONGTEXT DEFAULT NULL,
  `portraitImage` LONGBLOB DEFAULT NULL,
  `bannerImage` LONGBLOB DEFAULT NULL,
  `availability` varchar(255) DEFAULT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `locations` varchar(255) DEFAULT NULL,
  `is_coach` bit DEFAULT 0,
  `allow_external` bit DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_solvers`
--
-- For solver certificate 
ALTER TABLE `tbl_solvers`
ADD COLUMN `certificates` TEXT;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_skills`
--

CREATE TABLE `tbl_skills` (
  `id` int(11) NOT NULL,
  `skill_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_skills`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_solver_skills`
--

CREATE TABLE `tbl_solver_skills` (
  `id` int(11) NOT NULL,
  `fk_solver_id` int(11) NOT NULL,
  `fk_skill_id` int(11) NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `level` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_solver_skills`
--

-- --------------------------------------------------

-- create tbl_solver_locations

CREATE TABLE `tbl_solver_locations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fk_solver_id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  FOREIGN KEY (`fk_solver_id`) REFERENCES `tbl_solvers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- --------------------------------------------------------

--
-- Table structure for table `tbl_opportunity_skills`
--

CREATE TABLE `tbl_opportunity_skills` (
  `id` int(11) NOT NULL,
  `fk_opportunity_id` int(11) NOT NULL,
  `fk_skill_id` int(11) NULL,
  `duration` int(11) DEFAULT NULL,
  `level` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_opportunity_skills`
--
-- --------------------------------------------------------

--
-- Table structure for table `tbl_credentials`
--

CREATE TABLE `tbl_credentials` (
  `id` int(11) NOT NULL,
  `fk_trainingpartner_id` int(11) DEFAULT NULL,
  `credential_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_credentials`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_solver_credentials`
--

CREATE TABLE `tbl_solver_credentials` (
  `id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  `fk_credential_id` int(11) NOT NULL,
  `completed` int(11) DEFAULT NULL,
  `level` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_solver_credentials`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_opportunity_credentials`
--

CREATE TABLE `tbl_opportunity_credentials` (
  `id` int(11) NOT NULL,
  `fk_opportunity_id` int(11) NOT NULL,
  `fk_credential_id` int(11) NOT NULL,
  `completed` int(11) DEFAULT NULL,
  `level` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_opportunity_credentials`
--
--
-- Table structure for table `tbl_smprofiles`
--

CREATE TABLE `tbl_smprofiles` (
  `id` BIGINT NOT NULL,
  `profile_name` varchar(255) NOT NULL,
  `profile_namespace_uri` varchar(255) NOT NULL,
  `profile_marketplace_id` INT(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_smprofiles`
--

INSERT INTO `tbl_smprofiles` (`profile_marketplace_id`, `profile_namespace_uri`, `profile_name`) VALUES
(2845904701, 'https://axiomsystems.io/profiles/ncdwireless', 'NCD Wireless Sensors');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_solver_smprofiles`
--

CREATE TABLE `tbl_solver_smprofiles` (
  `id` int(11) NOT NULL,
  `fk_solver_id` int(11) NOT NULL,
  `fk_profile_id` int(11) NOT NULL,
  `last_activity` int(11) DEFAULT NULL,
  `level` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_solver_smprofiles`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_opportunity_smprofiles`
--

CREATE TABLE `tbl_opportunity_smprofiles` (
  `id` int(11) NOT NULL,
  `fk_opportunity_id` int(11) NOT NULL,
  `fk_profile_id` int(11) NOT NULL,
  `last_activity` int(11) DEFAULT NULL,
  `level` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_opportunity_smprofiles`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_opportunities`
--

CREATE TABLE `tbl_opportunities` (
  `id` varchar(30) NOT NULL,
  `fk_user_id` varchar(30) NOT NULL,
  `fk_org_id` varchar(30) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `requirements` LONGTEXT DEFAULT NULL,
  `start_date` varchar(255) DEFAULT NULL,
  `complete_date` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `Address_line_1` varchar(255) DEFAULT NULL,
  `Address_line_2` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `State` varchar(50) DEFAULT NULL,
  `Zip_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_opportunities`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_organizations`
--

CREATE TABLE `tbl_organizations` (
  `id` int(11) NOT NULL,
  `orgname` varchar(255) DEFAULT NULL,
  `creator` varchar(30) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `orgtype` tinyint(4) DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `Address_line_1` varchar(255) DEFAULT NULL,
  `Address_line_2` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `State` varchar(50) DEFAULT NULL,
  `Zip_code` varchar(20) DEFAULT NULL,
  `precise_location` POINT DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_organizations`
--

INSERT INTO `tbl_organizations` (`id`, `orgname`, `creator`, `orgtype`, `description`, `location`, `website`, `created_at`, `updated_at`) VALUES
(1, 'Bennit Inc', 1, 0, 'Default Bennit organization', 'Novelty, Ohio', 'https://www.bennit.ai', '2023-08-16 16:23:01', '2023-08-16 16:23:01');


-- --------------------------------------------------------

--
-- Table structure for table `tbl_organization_users`
--

CREATE TABLE `tbl_organization_users` (
  `id` int(11) NOT NULL,
  `fk_org_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  `org_level` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_organizations`
--

INSERT INTO tbl_organization_users (fk_org_id, fk_user_id, org_level) VALUES (1,1,1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_matches`
--

CREATE TABLE `tbl_matches` (
  `id` int(11) NOT NULL,
  `public_id` varchar(30) NOT NULL,
  `fk_opportunity_id` varchar(30) NOT NULL,
  `fk_solver_id` varchar(30) NOT NULL,
  `matched_by` varchar(30) NOT NULL,
  `seeker_viewed` timestamp DEFAULT NULL,
  `solver_viewed` timestamp DEFAULT NULL,
  `seeker_match` varchar(30) DEFAULT 0,
  `solver_match` varchar(30) DEFAULT 0,
  `matchmaker_approved` varchar(30) DEFAULT 0,
  `seeker_solver_connect` timestamp DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_matches`
--

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_roles`
--
ALTER TABLE `tbl_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_subscriptions`
--
ALTER TABLE `tbl_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_solvers`
--
ALTER TABLE `tbl_solvers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_opportunities`
--
ALTER TABLE `tbl_opportunities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_skills`
--
ALTER TABLE `tbl_skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_solver_skills`
--
ALTER TABLE `tbl_solver_skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_opportunity_skills`
--
ALTER TABLE `tbl_opportunity_skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_credentials`
--
ALTER TABLE `tbl_credentials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_solver_credentials`
--
ALTER TABLE `tbl_solver_credentials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_opportunity_credentials`
--
ALTER TABLE `tbl_opportunity_credentials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_smprofiles`
--
ALTER TABLE `tbl_smprofiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_solver_smprofiles`
--
ALTER TABLE `tbl_solver_smprofiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_opportunity_smprofiles`
--
ALTER TABLE `tbl_opportunity_smprofiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_organizations`
--
ALTER TABLE `tbl_organizations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_organization_users`
--
ALTER TABLE `tbl_organization_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_matches`
--
ALTER TABLE `tbl_matches`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_roles`
--
ALTER TABLE `tbl_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'role_id', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_subscriptions`
--
ALTER TABLE `tbl_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_solvers`
--
ALTER TABLE `tbl_solvers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_opportunities`
--
ALTER TABLE `tbl_opportunities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_skills`
--
ALTER TABLE `tbl_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_solver_skills`
--
ALTER TABLE `tbl_solver_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_opportunity_skills`
--
ALTER TABLE `tbl_opportunity_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_credentials`
--
ALTER TABLE `tbl_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_solver_credentials`
--
ALTER TABLE `tbl_solver_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_opportunity_credentials`
--
ALTER TABLE `tbl_opportunity_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_smprofiles`
--
ALTER TABLE `tbl_smprofiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_solver_smprofiles`
--
ALTER TABLE `tbl_solver_smprofiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_opportunity_smprofiles`
--
ALTER TABLE `tbl_opportunity_smprofiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_organizations`
--
ALTER TABLE `tbl_organizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_organization_users`
--
ALTER TABLE `tbl_organization_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

--
-- AUTO_INCREMENT for table `tbl_matches`
--
ALTER TABLE `tbl_matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;
