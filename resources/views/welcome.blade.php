<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'LMS') }} - English Microlearning Platform</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm fixed w-full z-50 border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <x-application-logo class="h-10 w-auto text-bass-red" />
                        <span class="ml-3 text-xl font-bold text-gray-900">English Hub</span>
                    </div>
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#features" class="text-gray-700 hover:text-bass-red transition-colors">Features</a>
                        <a href="#courses" class="text-gray-700 hover:text-bass-red transition-colors">Courses</a>
                        <a href="#testimonials" class="text-gray-700 hover:text-bass-red transition-colors">Reviews</a>
                        <a href="#pricing" class="text-gray-700 hover:text-bass-red transition-colors">Pricing</a>
                        <a href="{{ route('login') }}" class="text-bass-red hover:text-red-700 font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-bass-red text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">Get Started</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="pt-20 pb-16 bg-gradient-to-br from-blue-50 via-white to-indigo-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-bass-red/10 text-bass-red text-sm font-medium mb-6">
                        <i class="fas fa-star mr-2"></i>
                        Trusted by 10,000+ learners worldwide
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 leading-tight">
                        Master English with
                        <span class="relative">
                            <span class="relative z-10 text-bass-red">Microlearning</span>
                            <span class="absolute bottom-2 left-0 w-full h-4 bg-bass-gold/70 -z-0"></span>
                        </span>
                    </h1>
                    <p class="mt-6 text-xl text-gray-600 max-w-3xl mx-auto">
                        Learn English efficiently with bite-sized lessons, interactive exercises, and AI-powered feedback.
                        Just 15 minutes a day to fluency.
                    </p>
                    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto bg-bass-red text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-red-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            <i class="fas fa-rocket mr-2"></i>Start Learning Free
                        </a>
                        <a href="#demo" class="w-full sm:w-auto border border-gray-300 text-gray-700 px-8 py-4 rounded-xl text-lg font-semibold hover:bg-gray-50 transition-colors">
                            <i class="fas fa-play mr-2"></i>Watch Demo
                        </a>
                    </div>
                    <div class="mt-8 flex justify-center items-center space-x-6 text-sm text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            No credit card required
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            14-day free trial
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <div class="text-3xl font-bold text-bass-red">10K+</div>
                        <div class="text-gray-600 mt-2">Active Learners</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-bass-red">50+</div>
                        <div class="text-gray-600 mt-2">Expert Instructors</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-bass-red">500+</div>
                        <div class="text-gray-600 mt-2">Micro Lessons</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-bass-red">95%</div>
                        <div class="text-gray-600 mt-2">Success Rate</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Why Choose Our Platform?</h2>
                    <p class="mt-4 text-xl text-gray-600">Everything you need to master English effectively</p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-clock text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Microlearning</h3>
                        <p class="text-gray-600">Learn in small, digestible chunks that fit into your busy schedule. Just 15 minutes per day.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-brain text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">AI-Powered</h3>
                        <p class="text-gray-600">Get personalized feedback and adaptive learning paths powered by advanced AI technology.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Community</h3>
                        <p class="text-gray-600">Connect with fellow learners, practice together, and get support from our vibrant community.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-certificate text-bass-red text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Certifications</h3>
                        <p class="text-gray-600">Earn recognized certificates to boost your career and showcase your English proficiency.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-mobile-alt text-yellow-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Mobile App</h3>
                        <p class="text-gray-600">Learn anywhere, anytime with our mobile app. Download lessons for offline learning.</p>
                    </div>
                    <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-chart-line text-indigo-600 text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Progress Tracking</h3>
                        <p class="text-gray-600">Monitor your progress with detailed analytics and celebrate your achievements along the way.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Courses Preview -->
        <section id="courses" class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Popular Courses</h2>
                    <p class="mt-4 text-xl text-gray-600">Start with our most loved microlearning paths</p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-2xl">
                        <div class="text-blue-600 text-3xl mb-4">🚀</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">English Basics</h3>
                        <p class="text-gray-600 mb-4">Perfect for beginners. Learn fundamental grammar, vocabulary, and pronunciation.</p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock mr-2"></i>30 lessons • 15 min each
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-2xl">
                        <div class="text-green-600 text-3xl mb-4">💼</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Business English</h3>
                        <p class="text-gray-600 mb-4">Master professional communication, presentations, and business vocabulary.</p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock mr-2"></i>45 lessons • 20 min each
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-2xl">
                        <div class="text-purple-600 text-3xl mb-4">🎯</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">IELTS/TOEFL Prep</h3>
                        <p class="text-gray-600 mb-4">Comprehensive preparation for international English proficiency tests.</p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock mr-2"></i>60 lessons • 25 min each
                        </div>
                    </div>
                </div>
                <div class="text-center mt-12">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-bass-red text-bass-red hover:bg-bass-red hover:text-white rounded-lg transition-colors">
                        View All Courses <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section id="testimonials" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">What Our Students Say</h2>
                    <p class="mt-4 text-xl text-gray-600">Join thousands of successful English learners</p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">"The microlearning approach is perfect for my busy schedule. I've improved my English significantly in just 3 months!"</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-semibold">SM</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Sarah Miller</div>
                                <div class="text-gray-500 text-sm">Marketing Manager</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">"AI feedback is incredibly accurate. It's like having a personal English tutor available 24/7."</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-green-600 font-semibold">JD</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">John Davis</div>
                                <div class="text-gray-500 text-sm">Software Engineer</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-4">"Passed my IELTS with band 8! The structured learning path and practice tests were exactly what I needed."</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-purple-600 font-semibold">AL</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Ana Lopez</div>
                                <div class="text-gray-500 text-sm">Graduate Student</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-bass-red">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    Ready to Master English?
                </h2>
                <p class="text-xl text-red-100 mb-8">
                    Join thousands of learners who are already improving their English skills with our proven microlearning method.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto bg-white text-bass-red px-8 py-4 rounded-xl text-lg font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all duration-200">
                        Start Your Free Trial
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto border border-white text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-white hover:text-bass-red transition-colors">
                        Sign In
                    </a>
                </div>
                <p class="mt-4 text-red-200 text-sm">
                    No credit card required • 14-day free trial • Cancel anytime
                </p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <x-application-logo class="h-8 w-auto text-bass-red" />
                            <span class="ml-2 text-xl font-bold">English Hub</span>
                        </div>
                        <p class="text-gray-400">Making English learning accessible, effective, and enjoyable for everyone.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-4">Courses</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white transition-colors">English Basics</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Business English</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">IELTS/TOEFL Prep</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Conversation Skills</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-4">Support</h3>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Contact Us</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Community</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-4">Connect</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2024 English Hub. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </body>
</html>