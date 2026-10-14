📦 Multi-Warehouse Inventory Management System

A robust, real-time inventory management system built with Laravel 12 (backend API) and Vue.js 3 with Vuetify 3 (frontend dashboard). Designed to streamline warehouse stock tracking, transfers, and inventory auditing across multiple locations, complete with secure internal management controls.

✨ Key Features

Multi-Warehouse Management: Track and manage inventory levels independently across multiple warehouse locations.

Stock In & Stock Out: Seamlessly record incoming shipments from suppliers and outgoing fulfillments or adjustments.

Warehouse-to-Warehouse Transfers: Transfer stock between locations smoothly while maintaining accurate system-wide totals (zero net stock alteration during transit/transfer).

Real-Time Stock Auditing: Built-in calculation engine that tracks individual warehouse stock counts as well as aggregate totals enterprise-wide.

Internal Whitelisting & Access Control: Restrict administrative access to authorized IP addresses and whitelisted user accounts for internal security.

Modern Material UI: Clean, responsive, and intuitive user interface crafted using Vuetify 3.

🔒 Internal Security & Whitelisting

To protect sensitive inventory operations and ensure internal management integrity, the system includes:

IP Whitelisting Middleware: Restrict dashboard and API access to authorized office or warehouse static IP addresses.

User & Role Whitelisting: Granular authorization to specify which user accounts or administrative roles can approve stock transfers, conduct audits, or modify warehouse configurations.

Access Audit Logs: Track all access attempts and modifications performed by whitelisted users for compliance and traceability.

🛠️ Tech Stack

Backend

PHP 8.2+

Laravel 12

MySQL / MariaDB

Laravel Sanctum (API Authentication)

Custom Middleware (IP & Access Whitelisting)

Frontend
Vue.js 3 (Composition API)

Vuetify 3 (Material Design Components)

Pinia (State Management)

Axios (HTTP Client)

🔄 Core Workflow Overview
Stock In: Log incoming stock items, assign them to a specific target warehouse, and instantly update localized and total quantities.

Stock Out: Process orders, damages, or write-offs from a designated warehouse.

Warehouse Transfer: Initiate a secure stock movement from Warehouse A to Warehouse B. The system deducts stock from the source and adds it to the destination, ensuring total company stock remains completely balanced.

Whitelisted Administration: Manage IP subnets and whitelisted manager accounts directly from the internal management panel.
