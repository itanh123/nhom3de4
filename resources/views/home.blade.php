<!DOCTYPE html>

<html class="light" lang="vi"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Plus+Jakarta+Sans:wght@600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary-fixed": "#f39cfb",
                        "inverse-surface": "#0b0f10",
                        "error-container": "#fb5151",
                        "surface-container-low": "#eef1f3",
                        "tertiary": "#883c93",
                        "secondary-container": "#c6cfff",
                        "surface-container": "#e5e9eb",
                        "secondary-fixed": "#c6cfff",
                        "on-secondary-container": "#1f3ea2",
                        "on-tertiary-fixed-variant": "#6a1f77",
                        "on-primary": "#f0f2ff",
                        "secondary-dim": "#2a47ab",
                        "on-primary-container": "#00214e",
                        "primary-dim": "#004da4",
                        "secondary-fixed-dim": "#b3c1ff",
                        "tertiary-dim": "#7a2f86",
                        "surface-container-high": "#dfe3e6",
                        "surface": "#f5f7f9",
                        "outline-variant": "#abadaf",
                        "primary": "#0058ba",
                        "surface-tint": "#0058ba",
                        "primary-fixed-dim": "#5091ff",
                        "on-secondary": "#f2f1ff",
                        "error": "#b31b25",
                        "on-error-container": "#570008",
                        "on-tertiary-container": "#60136d",
                        "primary-container": "#6c9fff",
                        "error-dim": "#9f0519",
                        "on-secondary-fixed": "#00298b",
                        "on-secondary-fixed-variant": "#2b48ac",
                        "on-primary-fixed-variant": "#002a60",
                        "surface-bright": "#f5f7f9",
                        "surface-container-lowest": "#ffffff",
                        "background": "#f5f7f9",
                        "inverse-on-surface": "#9a9d9f",
                        "on-error": "#ffefee",
                        "primary-fixed": "#6c9fff",
                        "inverse-primary": "#4a8eff",
                        "on-tertiary-fixed": "#41004d",
                        "outline": "#747779",
                        "surface-dim": "#d0d5d8",
                        "on-tertiary": "#ffeefb",
                        "secondary": "#3854b7",
                        "on-background": "#2c2f31",
                        "on-surface-variant": "#595c5e",
                        "on-surface": "#2c2f31",
                        "tertiary-container": "#f39cfb",
                        "surface-variant": "#d9dde0",
                        "surface-container-highest": "#d9dde0",
                        "tertiary-fixed-dim": "#e48fed",
                        "on-primary-fixed": "#000000"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "fontFamily": {
                        "headline": ["Plus Jakarta Sans"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                }
            }
        }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3 { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .signature-gradient {
            background: linear-gradient(135deg, #0058ba 0%, #6c9fff 100%);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-surface text-on-surface antialiased">
<!-- Header / Navigation -->
<header class="fixed top-0 w-full z-50 glass-nav shadow-sm shadow-slate-200/50">
<div class="flex justify-between items-center max-w-7xl mx-auto px-6 h-16">
<div class="flex items-center gap-8">
<a class="text-xl font-bold tracking-tight text-primary" href="#">Lumina Quiz</a>
<nav class="hidden md:flex items-center gap-6">
<a class="text-primary font-semibold border-b-2 border-primary py-1" href="#">Trang chủ</a>
<a class="text-on-surface-variant hover:text-primary transition-colors py-1" href="#">Danh mục</a>
<a class="text-on-surface-variant hover:text-primary transition-colors py-1" href="#">Lịch sử</a>
</nav>
</div>
<div class="flex items-center gap-4">
<button class="px-4 py-2 text-primary font-medium hover:bg-surface-container-low transition-all duration-200 rounded-xl active:scale-95">Đăng nhập</button>
<button class="px-5 py-2 signature-gradient text-on-primary font-semibold rounded-xl shadow-lg shadow-primary/20 hover:shadow-primary/30 transition-all duration-200 active:scale-95">Bắt đầu ngay</button>
</div>
</div>
</header>
<main class="pt-16">
<!-- Hero Section -->
<section class="relative overflow-hidden bg-surface py-20 lg:py-32">
<div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
<div class="space-y-8 z-10">
<div class="inline-flex items-center gap-2 px-3 py-1 bg-secondary-container text-on-secondary-container rounded-full text-xs font-semibold tracking-wide uppercase">
<span class="material-symbols-outlined text-sm">auto_awesome</span>
                        Nền tảng học tập mới
                    </div>
<h1 class="text-5xl lg:text-7xl font-extrabold text-on-surface leading-[1.1] tracking-tight">
                        Chinh Phục <br/> <span class="text-primary">Kiến Thức</span>
</h1>
<p class="text-lg text-on-surface-variant max-w-lg leading-relaxed">
                        Nâng cao trí tuệ mỗi ngày thông qua các bộ đề trắc nghiệm chuyên sâu. Trải nghiệm học tập tinh gọn, hiệu quả và đầy cảm hứng.
                    </p>
<div class="flex flex-col sm:flex-row gap-4 pt-4">
<button class="px-8 py-4 signature-gradient text-on-primary text-lg font-bold rounded-xl shadow-xl shadow-primary/25 hover:shadow-primary/40 transition-all duration-300 active:scale-95 flex items-center justify-center gap-2">
                            Bắt đầu ngay
                            <span class="material-symbols-outlined">arrow_forward</span>
</button>
<button class="px-8 py-4 bg-surface-container-lowest text-on-surface text-lg font-semibold rounded-xl hover:bg-surface-container-low transition-all duration-300 flex items-center justify-center gap-2 border border-outline-variant/15">
                            Tìm hiểu thêm
                        </button>
</div>
</div>
<div class="relative">
<div class="absolute -top-20 -right-20 w-96 h-96 bg-primary-container/20 rounded-full blur-3xl"></div>
<div class="absolute -bottom-20 -left-20 w-72 h-72 bg-tertiary-container/20 rounded-full blur-3xl"></div>
<div class="relative bg-surface-container-lowest rounded-[2rem] p-4 shadow-2xl shadow-on-surface/5 border border-outline-variant/10 transform rotate-2">
<img alt="Student learning" class="rounded-[1.5rem] w-full h-[400px] object-cover" data-alt="A diverse group of modern university students collaborating in a bright, airy library with glass walls and minimalist furniture" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC04seJi89Od8iphOn0uWbwvAu8HoazRP-oQ8UzHBIZ5v9Gn3Tnbtm0-5jXICPOufRdfSuc1o_hjhUej7Up6dKICkbBSpkFC1fG0AITM86gIYeAhXScA33CHYgoskmh-49bAtzxh_e7zEIdbNveNsUjNiMadk-5JtcOBFY2IZlriYx9oNoDQvbphpP4xpAAPm-yUa-gji36DejlwUpGw3IrpnqB_Gzap7kjLC5UCgoMQzR7sLUBG9435RY1zh0XUnT_9nLRpEk8kdE"/>
<div class="absolute -bottom-6 -left-6 bg-surface-container-lowest p-5 rounded-2xl shadow-xl border border-outline-variant/10">
<div class="flex items-center gap-3">
<div class="w-12 h-12 bg-primary-container flex items-center justify-center rounded-full">
<span class="material-symbols-outlined text-primary" data-weight="fill">verified</span>
</div>
<div>
<p class="text-sm font-bold">1,200+ Học viên</p>
<p class="text-xs text-on-surface-variant">Đã tham gia hôm nay</p>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Quiz Catalog Section -->
<section class="py-24 bg-surface-container-low">
<div class="max-w-7xl mx-auto px-6">
<div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
<div class="space-y-4">
<h2 class="text-3xl md:text-4xl font-extrabold text-on-surface">Các bộ đề nổi bật</h2>
<p class="text-on-surface-variant max-w-xl">Lựa chọn các chủ đề kiến thức đang được quan tâm nhất hiện nay để thử thách bản thân.</p>
</div>
<button class="text-primary font-bold flex items-center gap-2 hover:gap-3 transition-all group">
                        Xem tất cả danh mục
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">trending_flat</span>
</button>
</div>
<!-- Bento Grid / Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
<!-- Card 1 -->
<div class="group bg-surface-container-lowest rounded-[1.5rem] p-6 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-outline-variant/10">
<div class="h-48 mb-6 overflow-hidden rounded-xl bg-slate-100">
<img alt="Education" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="Modern laboratory equipment with blue neon lighting focusing on a microscope in a high-tech science lab" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB3CUr4fi6NK-YSIdL35lGPytE36d5ukrEBuTDBb8_p4CaCJbtz6Hh2ddBnU5Mvpx05PLS8e_wyS49lUgun4-yWKFWY6Jr-x5FV8hu2VEXQufABGPV7vvJms2vBZkTBxdfznFnymVMfIxkGhBfrFAIlDIDsAilWzBRUM8reBrlUE4kHb0C4oGRBMGxvQPXD5B6LH01OCMD91Om8i4UAkBhVq7HgW5J6x0HdMJgC6afVoytggfx7wNk4dtA0Y2ffvdLX9i_n5JJl_x8"/>
</div>
<div class="space-y-4">
<h3 class="text-xl font-bold text-on-surface group-hover:text-primary transition-colors">Kiến thức Khoa học Tự nhiên</h3>
<div class="flex items-center gap-4 text-on-surface-variant text-sm">
<div class="flex items-center gap-1.5 bg-surface-container px-3 py-1 rounded-full">
<span class="material-symbols-outlined text-sm">quiz</span>
                                    25 câu
                                </div>
<div class="flex items-center gap-1.5 bg-surface-container px-3 py-1 rounded-full">
<span class="material-symbols-outlined text-sm">schedule</span>
                                    20 phút
                                </div>
</div>
<button class="w-full py-3 signature-gradient text-on-primary font-bold rounded-xl active:scale-95 transition-transform">
                                Làm bài
                            </button>
</div>
</div>
<!-- Card 2 -->
<div class="group bg-surface-container-lowest rounded-[1.5rem] p-6 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-outline-variant/10">
<div class="h-48 mb-6 overflow-hidden rounded-xl bg-slate-100">
<img alt="Technology" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="Abstract digital background with glowing blue circuit lines and binary code representing cybersecurity and technology" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAItOASHnXiqzqze7qW7Fg7v3eFM634SIxiJBIlUnM6aZsb1h6Rc3Tf-n2ufku_dHEkZJww2KTcG2tAdK7JlYjHOFiKKbLi5UfWJYgeefaDW2E5IMQm06ZFRM4FiyzMDk-O8NnPkZiHB1Kk6h_blFzs1z46XwLoNjBdLJb56OPTgzsi92ASrlaRboIRQ1UuqTnl9ugjhT2hzhlQDAqLVLztHiFtd_qvArgqJtMxX9qM5WVdI3KI0PF7ORAYaMGoY2L_pHApAxztY9o"/>
</div>
<div class="space-y-4">
<h3 class="text-xl font-bold text-on-surface group-hover:text-primary transition-colors">Công nghệ &amp; Đổi mới</h3>
<div class="flex items-center gap-4 text-on-surface-variant text-sm">
<div class="flex items-center gap-1.5 bg-surface-container px-3 py-1 rounded-full">
<span class="material-symbols-outlined text-sm">quiz</span>
                                    20 câu
                                </div>
<div class="flex items-center gap-1.5 bg-surface-container px-3 py-1 rounded-full">
<span class="material-symbols-outlined text-sm">schedule</span>
                                    15 phút
                                </div>
</div>
<button class="w-full py-3 signature-gradient text-on-primary font-bold rounded-xl active:scale-95 transition-transform">
                                Làm bài
                            </button>
</div>
</div>
<!-- Card 3 -->
<div class="group bg-surface-container-lowest rounded-[1.5rem] p-6 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-outline-variant/10">
<div class="h-48 mb-6 overflow-hidden rounded-xl bg-slate-100">
<img alt="History" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" data-alt="Cinematic shot of ancient Greek columns at sunset with warm amber light and deep blue shadows" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBadJLlDBUxBijgmcRkhCvjqZPUZjBKAkfUWbWglxtYSyCqYMTOOm8VM0PSr9MWNdMRhv1tQ1hp5EEXDsLmhUIEfBa4y1FNJMbrl2k1LA_TQFl20yk1_h4cAXMvYL9SFp3Bn2YO3217NC5lKL1U5L-9RbDzXMTeIB-cjFw6iWqqQKa84B7HHzZFomsWimE466fYE_YRTNi7Obk7ybg0Qv28mAB7G-ZEuSHhSpGtPl-aehudgDt9j2twEWAIQiUzSFUqxN1EkBrVvy8"/>
</div>
<div class="space-y-4">
<h3 class="text-xl font-bold text-on-surface group-hover:text-primary transition-colors">Lịch sử Thế giới Cận đại</h3>
<div class="flex items-center gap-4 text-on-surface-variant text-sm">
<div class="flex items-center gap-1.5 bg-surface-container px-3 py-1 rounded-full">
<span class="material-symbols-outlined text-sm">quiz</span>
                                    30 câu
                                </div>
<div class="flex items-center gap-1.5 bg-surface-container px-3 py-1 rounded-full">
<span class="material-symbols-outlined text-sm">schedule</span>
                                    25 phút
                                </div>
</div>
<button class="w-full py-3 signature-gradient text-on-primary font-bold rounded-xl active:scale-95 transition-transform">
                                Làm bài
                            </button>
</div>
</div>
</div>
</div>
</section>
<!-- Stats Section -->
<section class="py-20 bg-surface">
<div class="max-w-7xl mx-auto px-6">
<div class="grid grid-cols-2 md:grid-cols-4 gap-8">
<div class="text-center space-y-2">
<p class="text-4xl font-extrabold text-primary">50k+</p>
<p class="text-on-surface-variant font-medium">Người dùng active</p>
</div>
<div class="text-center space-y-2">
<p class="text-4xl font-extrabold text-primary">1,500+</p>
<p class="text-on-surface-variant font-medium">Bộ đề đa dạng</p>
</div>
<div class="text-center space-y-2">
<p class="text-4xl font-extrabold text-primary">4.9/5</p>
<p class="text-on-surface-variant font-medium">Đánh giá hài lòng</p>
</div>
<div class="text-center space-y-2">
<p class="text-4xl font-extrabold text-primary">24/7</p>
<p class="text-on-surface-variant font-medium">Hỗ trợ học tập</p>
</div>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-surface-container-lowest border-t border-outline-variant/10">
<div class="max-w-7xl mx-auto px-6 py-16">
<div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
<div class="col-span-1 md:col-span-2 space-y-6">
<a class="text-2xl font-bold text-primary" href="#">Lumina Quiz</a>
<p class="text-on-surface-variant max-w-sm leading-relaxed">
                        Nâng tầm kiến thức của bạn thông qua phương pháp trắc nghiệm hiện đại. Chúng tôi tin rằng việc học nên là một trải nghiệm thú vị và không ngừng nghỉ.
                    </p>
<div class="flex gap-4">
<a class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary hover:bg-primary hover:text-on-primary transition-all duration-300" href="#">
<span class="material-symbols-outlined text-xl">social_leaderboard</span>
</a>
<a class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary hover:bg-primary hover:text-on-primary transition-all duration-300" href="#">
<span class="material-symbols-outlined text-xl">camera</span>
</a>
<a class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary hover:bg-primary hover:text-on-primary transition-all duration-300" href="#">
<span class="material-symbols-outlined text-xl">alternate_email</span>
</a>
</div>
</div>
<div>
<h4 class="font-bold mb-6">Liên kết</h4>
<ul class="space-y-4 text-on-surface-variant">
<li><a class="hover:text-primary transition-colors" href="#">Trang chủ</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Danh mục</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Lịch sử</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Bảng xếp hạng</a></li>
</ul>
</div>
<div>
<h4 class="font-bold mb-6">Hỗ trợ</h4>
<ul class="space-y-4 text-on-surface-variant">
<li><a class="hover:text-primary transition-colors" href="#">Trung tâm trợ giúp</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Điều khoản dịch vụ</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Chính sách bảo mật</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Liên hệ</a></li>
</ul>
</div>
</div>
<div class="pt-8 border-t border-outline-variant/10 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-on-surface-variant">
<p>© 2024 Lumina Quiz. Tất cả quyền được bảo lưu.</p>
<div class="flex gap-6">
<span>Tiếng Việt (VN)</span>
<span>English (US)</span>
</div>
</div>
</div>
</footer>
</body></html>
