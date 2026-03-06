<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <title>Vet Clinics</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/daterangepicker.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css" />

    <style>
        /* Chat styles */
        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }
        .message {
            padding: 8px 12px;
            border-radius: 15px;
            margin-bottom: 10px;
            max-width: 70%;
        }
        .message.user {
            background-color: #e5e5ea;
            align-self: flex-start;
        }
        .message.admin {
            background-color: #0d6efd;
            color: #fff;
            align-self: flex-end;
        }
        .chat-main {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .chat-header {
            font-weight: bold;
            font-size: 1.1rem;
        }
        .chat-sidebar a.active {
            background-color: #0d6efd;
            color: #fff;
        }
        .chat-sidebar a {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- Navigation Menu -->
    <?php include 'partials/sidebar.php'; ?>

    <!-- Header -->
    <header class="nxl-header">
        <div class="header-wrapper">
            <div class="header-left d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse"></a>
                <div class="nxl-navigation-toggle">
                    <a href="javascript:void(0);" id="menu-mini-button"><i class="feather-align-left"></i></a>
                    <a href="javascript:void(0);" id="menu-expend-button" style="display: none"><i class="feather-arrow-right"></i></a>
                </div>
            </div>

            <div class="header-right ms-auto">
                <div class="d-flex align-items-center">
                    <div class="dropdown nxl-h-item nxl-header-search">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="feather-search"></i>
                        </a>
                    </div>

                    <div class="nxl-h-item dark-light-theme">
                        <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button"><i class="feather-moon"></i></a>
                        <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none"><i class="feather-sun"></i></a>
                    </div>

                    <div class="dropdown nxl-h-item">
                        <a class="nxl-head-link me-3" data-bs-toggle="dropdown" href="#" role="button" data-bs-auto-close="outside">
                            <i class="feather-bell"></i>
                            <span class="badge bg-danger nxl-h-badge">3</span>
                        </a>
                    </div>

                    <div class="dropdown nxl-h-item">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                            <img src="assets/images/avatar/1.png" alt="user-image" class="img-fluid user-avtar me-0" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item">Messages</li>
                    </ul>
                </div>
            </div>

            <!-- Chat Section -->
            <div class="main-content">
                <div class="row">
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-body p-0">
                                <div class="row g-0 chat-container">

                                    <!-- Conversation List -->
                                    <div class="col-md-4 col-lg-3 border-end chat-sidebar" style="background:#f5f5f5;">
                                        <div class="p-3 border-bottom fw-bold">Conversations</div>
                                        <div class="list-group list-group-flush chat-conversations">
                                            <a class="list-group-item list-group-item-action active" data-user="charlie">
                                                <strong>Charlie’s Owner</strong><br>
                                                <small>Concern about feeding</small>
                                            </a>
                                            <a class="list-group-item list-group-item-action" data-user="mia">
                                                <strong>Mia’s Family</strong><br>
                                                <small>Vaccination follow-up</small>
                                            </a>
                                            <a class="list-group-item list-group-item-action" data-user="oliver">
                                                <strong>Oliver’s Owner</strong><br>
                                                <small>Medication question</small>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Chat Window -->
                                    <div class="col-md-8 col-lg-9 d-flex flex-column chat-main">
                                        <div class="p-3 border-bottom fw-bold chat-header">Charlie’s Owner</div>
                                        <div class="flex-grow-1 p-3 chat-messages" style="overflow-y:auto; background:#fff;"></div>

                                        <div class="p-3 border-top">
                                            <div class="input-group">
                                                <input type="text" class="form-control chat-input" placeholder="Type your message...">
                                                <button class="btn btn-primary chat-send">Send</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <!-- Vendors JS -->
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/vendors/js/daterangepicker.min.js"></script>
    <script src="assets/vendors/js/apexcharts.min.js"></script>
    <script src="assets/vendors/js/circle-progress.min.js"></script>

    <!-- Apps Init -->
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/dashboard-init.min.js"></script>
    <script src="assets/js/theme-customizer-init.min.js"></script>

    <!-- Chat JS -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const conversations = document.querySelectorAll(".chat-conversations a");
            const chatHeader = document.querySelector(".chat-header");
            const chatMessages = document.querySelector(".chat-messages");
            const input = document.querySelector(".chat-input");
            const sendBtn = document.querySelector(".chat-send");

            const chats = {
                charlie: [
                    { sender: "user", text: "Hi there! Charlie isn’t eating well today." },
                    { sender: "admin", text: "Thanks for contacting us. What symptoms are you seeing?" },
                    { sender: "user", text: "He seems lethargic and skipped breakfast." }
                ],
                mia: [
                    { sender: "user", text: "Hello, Mia's vaccination update." },
                    { sender: "admin", text: "All vaccines are up to date." }
                ],
                oliver: [
                    { sender: "user", text: "Is Oliver okay with his meds?" },
                    { sender: "admin", text: "Yes, please follow the prescription instructions." }
                ]
            };

            let currentUser = "charlie";

            function renderChat(user) {
                chatMessages.innerHTML = "";
                chats[user].forEach(msg => {
                    const div = document.createElement("div");
                    div.className = "message " + msg.sender;
                    div.textContent = msg.text;
                    chatMessages.appendChild(div);
                });
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            function appendMessage(text, sender) {
                const div = document.createElement("div");
                div.className = "message " + sender;
                div.textContent = text;
                chatMessages.appendChild(div);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            sendBtn.addEventListener("click", () => {
                const text = input.value.trim();
                if (!text) return;
                appendMessage(text, "admin");
                chats[currentUser].push({ sender: "admin", text });
                input.value = "";

                setTimeout(() => {
                    const autoReplies = [
                        "Thank you for your message. We'll check that.",
                        "Can you provide more details?",
                        "Got it! We'll handle it shortly.",
                        "Please monitor your pet closely.",
                        "We recommend scheduling a clinic visit."
                    ];
                    const reply = autoReplies[Math.floor(Math.random() * autoReplies.length)];
                    appendMessage(reply, "user");
                    chats[currentUser].push({ sender: "user", text: reply });
                }, 1000);
            });

            input.addEventListener("keydown", (e) => {
                if (e.key === "Enter") sendBtn.click();
            });

            conversations.forEach(conv => {
                conv.addEventListener("click", () => {
                    conversations.forEach(c => c.classList.remove("active"));
                    conv.classList.add("active");
                    currentUser = conv.dataset.user;
                    chatHeader.textContent = conv.querySelector("strong").textContent;
                    renderChat(currentUser);
                });
            });

            renderChat(currentUser);
        });
    </script>

</body>

</html>
