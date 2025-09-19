{{-- resources/views/frontend/outletitemlist.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RE:GEN:TA CENTRAL - Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e91e63',
                        secondary: '#f06292',
                    }
                }
            }
        }
    </script>
    <style>
        .backdrop-blur {
            backdrop-filter: blur(10px);
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-400 via-pink-500 to-red-500 min-h-screen">
    
    <!-- Mobile Menu Toggle -->
    <button id="mobile-menu-toggle" class="fixed top-4 left-4 z-50 bg-white/90 backdrop-blur p-3 rounded-full shadow-lg md:hidden">
        <i class="fas fa-bars text-gray-700"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed left-0 top-0 h-full w-80 bg-white/95 backdrop-blur transform -translate-x-full transition-transform duration-300 z-40 shadow-2xl md:translate-x-0">
        <!-- Sidebar Header -->
        <div class="bg-gradient-to-r from-primary to-secondary p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-utensils text-xl"></i>
                    <h2 class="text-xl font-bold">RE:GEN:TA CENTRAL</h2>
                </div>
                <button id="close-sidebar" class="md:hidden">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Search Section -->
        <div class="p-6 border-b border-gray-200">
            <div class="relative">
                <input type="text" id="search-input" placeholder="Search menu items..." 
                       class="w-full pl-4 pr-12 py-3 border-2 border-gray-200 rounded-full focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Menu Navigation -->
        <div class="p-6">
            <nav class="space-y-2">
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors border-l-4 border-transparent hover:border-primary">
                    <i class="fas fa-home text-gray-600"></i>
                    <span class="text-gray-700 font-medium">Menu</span>
                </a>
                <a href="#" id="wishlist-btn" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors border-l-4 border-transparent hover:border-primary">
                    <i class="fas fa-heart text-gray-600"></i>
                    <span class="text-gray-700 font-medium">Wishlist</span>
                    <span id="wishlist-count" class="ml-auto bg-primary text-white text-xs px-2 py-1 rounded-full hidden">0</span>
                </a>
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors border-l-4 border-transparent hover:border-primary">
                    <i class="fas fa-info-circle text-gray-600"></i>
                    <span class="text-gray-700 font-medium">Resto Information</span>
                </a>
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 transition-colors border-l-4 border-transparent hover:border-primary">
                    <i class="fas fa-file-contract text-gray-600"></i>
                    <span class="text-gray-700 font-medium">Terms & Conditions</span>
                </a>
            </nav>
        </div>

        <!-- Filter Section -->
        <div class="p-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Filters</h3>
            
            <!-- Dish Type Filter -->
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Dish Type</h4>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" id="filter-all" class="filter-dishtype rounded border-gray-300 text-primary focus:ring-primary" checked>
                        <span class="text-sm text-gray-600">All Items</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" id="filter-veg" class="filter-dishtype rounded border-gray-300 text-primary focus:ring-primary" value="1">
                        <span class="text-sm text-gray-600">Vegetarian</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" id="filter-nonveg" class="filter-dishtype rounded border-gray-300 text-primary focus:ring-primary" value="0">
                        <span class="text-sm text-gray-600">Non-Vegetarian</span>
                    </label>
                </div>
            </div>

            <!-- Price Range Filter -->
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Price Range</h4>
                <div class="space-y-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="filter-price rounded border-gray-300 text-primary focus:ring-primary" value="0-200">
                        <span class="text-sm text-gray-600">Under ₹200</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="filter-price rounded border-gray-300 text-primary focus:ring-primary" value="200-500">
                        <span class="text-sm text-gray-600">₹200 - ₹500</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="filter-price rounded border-gray-300 text-primary focus:ring-primary" value="500-1000">
                        <span class="text-sm text-gray-600">₹500 - ₹1000</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" class="filter-price rounded border-gray-300 text-primary focus:ring-primary" value="1000+">
                        <span class="text-sm text-gray-600">Above ₹1000</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="md:ml-80 transition-all duration-300">
        <!-- Header -->
        <header class="bg-white/95 backdrop-blur shadow-lg p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-utensils mr-3 text-primary"></i>
                    RE:GEN:TA CENTRAL
                </h1>
                <div class="flex items-center space-x-4">
                    <button id="view-wishlist" class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-full hover:shadow-lg transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-heart"></i>
                        <span>Wishlist</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="m-6 h-64 bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl flex items-center justify-center text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-black/30"></div>
            <div class="text-center z-10">
                <h2 class="text-4xl font-bold mb-2">Delicious Menu</h2>
                <p class="text-lg opacity-90">Taxes As Applicable Extra</p>
            </div>
        </section>

        <!-- Category Tabs -->
        <section class="mx-6 mb-6">
            <div class="bg-white/95 backdrop-blur rounded-2xl p-6 shadow-xl">
                <div id="category-tabs" class="flex space-x-2 overflow-x-auto scrollbar-hide pb-2">
                    <button class="category-tab px-6 py-3 rounded-full bg-gradient-to-r from-primary to-secondary text-white font-medium whitespace-nowrap transition-all" data-group="all">
                        All Items
                    </button>
                    @php
                        $uniqueGroups = $items->unique('group_name')->pluck('group_name', 'item_group_code');
                    @endphp
                    @foreach($uniqueGroups as $groupCode => $groupName)
                        @if($groupName)
                        <button class="category-tab px-6 py-3 rounded-full bg-white border-2 border-gray-200 text-gray-700 font-medium whitespace-nowrap hover:border-primary hover:text-primary transition-all" data-group="{{ $groupCode }}">
                            {{ $groupName }}
                        </button>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Menu Items -->
        <section class="mx-6 mb-6">
            <div id="items-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($items as $item)
                <div class="menu-item bg-white/95 backdrop-blur rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1" 
                     data-group="{{ $item->item_group_code }}" 
                     data-dishtype="{{ $item->dishtype }}" 
                     data-price="{{ $item->item_rate }}"
                     data-name="{{ strtolower($item->item_name) }}">
                    
                    <!-- Item Image -->
                    <div class="relative h-48 overflow-hidden">
                        @if($item->itempic)
                            <img src="http://localhost:8000/storage/property/itempicture/{{ $item->itempic }}" 
                                 alt="{{ $item->item_name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                <i class="fas fa-utensils text-4xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <!-- Wishlist Button -->
                        <button class="wishlist-btn absolute top-4 right-4 w-10 h-10 bg-white/90 backdrop-blur rounded-full flex items-center justify-center shadow-lg hover:bg-white transition-all" 
                                data-item-name="{{ $item->item_name }}" 
                                data-item-price="{{ $item->item_rate }}" 
                                data-item-image="{{ $item->itempic }}">
                            <i class="fas fa-heart text-gray-400 hover:text-red-500 transition-colors"></i>
                        </button>
                    </div>

                    <!-- Item Details -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $item->item_name }}</h3>
                                <div class="flex items-center space-x-2 mb-3">
                                    @if($item->dishtype == 1)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-circle text-green-500 mr-1" style="font-size: 6px;"></i>
                                            Veg
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-circle text-red-500 mr-1" style="font-size: 6px;"></i>
                                            Non-Veg
                                        </span>
                                    @endif
                                    @if($item->group_name)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item->group_name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-2xl font-bold text-primary">
                                ₹{{ number_format($item->item_rate, 0) }}
                            </div>
                            <button class="add-to-cart bg-gradient-to-r from-primary to-secondary text-white px-6 py-2 rounded-full hover:shadow-lg transition-all duration-300 flex items-center space-x-2">
                                <i class="fas fa-plus"></i>
                                <span>Add</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- No Items Found Message -->
            <div id="no-items-message" class="hidden text-center py-12">
                <div class="bg-white/95 backdrop-blur rounded-2xl p-8 shadow-xl">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">No items found</h3>
                    <p class="text-gray-500">Try adjusting your filters or search terms</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Wishlist Modal -->
    <div id="wishlist-modal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-800">My Wishlist</h2>
                    <button id="close-wishlist-modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            <div id="wishlist-items" class="p-6">
                <!-- Wishlist items will be populated here -->
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
        updateWishlistUI();

        // Mobile menu toggle
        $('#mobile-menu-toggle').click(function() {
            $('#sidebar').addClass('translate-x-0').removeClass('-translate-x-full');
        });

        $('#close-sidebar').click(function() {
            $('#sidebar').addClass('-translate-x-full').removeClass('translate-x-0');
        });

        // Close sidebar when clicking outside on mobile
        $(document).click(function(e) {
            if ($(window).width() < 768 && !$(e.target).closest('#sidebar, #mobile-menu-toggle').length) {
                $('#sidebar').addClass('-translate-x-full').removeClass('translate-x-0');
            }
        });

        // Category tabs
        $('.category-tab').click(function() {
            $('.category-tab').removeClass('bg-gradient-to-r from-primary to-secondary text-white').addClass('bg-white border-2 border-gray-200 text-gray-700');
            $(this).removeClass('bg-white border-2 border-gray-200 text-gray-700').addClass('bg-gradient-to-r from-primary to-secondary text-white');
            
            const selectedGroup = $(this).data('group');
            filterItems();
        });

        // Search functionality
        $('#search-input').on('input', function() {
            filterItems();
        });

        // Filter functionality
        $('.filter-dishtype, .filter-price').change(function() {
            filterItems();
        });

        function filterItems() {
            const searchTerm = $('#search-input').val().toLowerCase();
            const selectedGroup = $('.category-tab.bg-gradient-to-r').data('group');
            const selectedDishTypes = [];
            const selectedPriceRanges = [];
            
            $('.filter-dishtype:checked').each(function() {
                if ($(this).val()) selectedDishTypes.push($(this).val());
            });
            
            $('.filter-price:checked').each(function() {
                selectedPriceRanges.push($(this).val());
            });

            let visibleCount = 0;

            $('.menu-item').each(function() {
                let show = true;
                const $item = $(this);
                const itemGroup = $item.data('group');
                const itemDishType = $item.data('dishtype').toString();
                const itemPrice = parseFloat($item.data('price'));
                const itemName = $item.data('name');

                // Group filter
                if (selectedGroup !== 'all' && itemGroup != selectedGroup) {
                    show = false;
                }

                // Search filter
                if (searchTerm && !itemName.includes(searchTerm)) {
                    show = false;
                }

                // Dish type filter
                if (selectedDishTypes.length > 0 && !selectedDishTypes.includes(itemDishType)) {
                    show = false;
                }

                // Price range filter
                if (selectedPriceRanges.length > 0) {
                    let priceMatch = false;
                    selectedPriceRanges.forEach(function(range) {
                        if (range === '0-200' && itemPrice < 200) priceMatch = true;
                        if (range === '200-500' && itemPrice >= 200 && itemPrice < 500) priceMatch = true;
                        if (range === '500-1000' && itemPrice >= 500 && itemPrice < 1000) priceMatch = true;
                        if (range === '1000+' && itemPrice >= 1000) priceMatch = true;
                    });
                    if (!priceMatch) show = false;
                }

                if (show) {
                    $item.show();
                    visibleCount++;
                } else {
                    $item.hide();
                }
            });

            // Show/hide no items message
            if (visibleCount === 0) {
                $('#no-items-message').removeClass('hidden');
            } else {
                $('#no-items-message').addClass('hidden');
            }
        }

        // Wishlist functionality
        $('.wishlist-btn').click(function(e) {
            e.stopPropagation();
            const itemName = $(this).data('item-name');
            const itemPrice = $(this).data('item-price');
            const itemImage = $(this).data('item-image');
            
            const item = {
                name: itemName,
                price: itemPrice,
                image: itemImage
            };

            const existingIndex = wishlist.findIndex(w => w.name === itemName);
            
            if (existingIndex > -1) {
                // Remove from wishlist
                wishlist.splice(existingIndex, 1);
                $(this).find('i').removeClass('text-red-500').addClass('text-gray-400');
            } else {
                // Add to wishlist
                wishlist.push(item);
                $(this).find('i').removeClass('text-gray-400').addClass('text-red-500');
            }
            
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            updateWishlistUI();
        });

        function updateWishlistUI() {
            // Update wishlist count
            const count = wishlist.length;
            if (count > 0) {
                $('#wishlist-count').text(count).removeClass('hidden');
            } else {
                $('#wishlist-count').addClass('hidden');
            }

            // Update wishlist button states
            $('.wishlist-btn').each(function() {
                const itemName = $(this).data('item-name');
                const isInWishlist = wishlist.some(w => w.name === itemName);
                
                if (isInWishlist) {
                    $(this).find('i').removeClass('text-gray-400').addClass('text-red-500');
                } else {
                    $(this).find('i').removeClass('text-red-500').addClass('text-gray-400');
                }
            });
        }

        // Show wishlist modal
        $('#view-wishlist, #wishlist-btn').click(function() {
            displayWishlistModal();
        });

        $('#close-wishlist-modal').click(function() {
            $('#wishlist-modal').addClass('hidden').removeClass('flex');
        });

        function displayWishlistModal() {
            let wishlistHTML = '';
            
            if (wishlist.length === 0) {
                wishlistHTML = '<div class="text-center py-8"><i class="fas fa-heart text-6xl text-gray-300 mb-4"></i><p class="text-gray-500 text-lg">Your wishlist is empty</p></div>';
            } else {
                wishlist.forEach(function(item, index) {
                    wishlistHTML += `
                        <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg mb-4">
                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                ${item.image ? `<img src="http://localhost:8000/storage/property/itempicture/${item.image}" alt="${item.name}" class="w-full h-full object-cover rounded-lg">` : '<i class="fas fa-utensils text-gray-400"></i>'}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-800">${item.name}</h4>
                                <p class="text-primary font-bold">₹${item.price}</p>
                            </div>
                            <button class="remove-from-wishlist text-red-500 hover:text-red-700" data-index="${index}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                });
            }
            
            $('#wishlist-items').html(wishlistHTML);
            $('#wishlist-modal').removeClass('hidden').addClass('flex');
        }

        // Remove from wishlist
        $(document).on('click', '.remove-from-wishlist', function() {
            const index = $(this).data('index');
            const itemName = wishlist[index].name;
            
            wishlist.splice(index, 1);
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
            
            updateWishlistUI();
            displayWishlistModal();
            
            // Update the wishlist button in the main grid
            $(`.wishlist-btn[data-item-name="${itemName}"]`).find('i').removeClass('text-red-500').addClass('text-gray-400');
        });

        // Close modal when clicking outside
        $('#wishlist-modal').click(function(e) {
            if (e.target === this) {
                $(this).addClass('hidden').removeClass('flex');
            }
        });

        // Initialize filters
        filterItems();
    });
    </script>
</body>
</html>