<div @class(['tp-product-item-2 mb-40', $class ?? null])>
    <div class="tp-product-thumb-2 p-relative z-index-1 fix w-img">
        <style>
        /* Hover effect: slide primary image out, secondary image in */
        .tp-product-thumb-2:hover img:not(.secondary) {
            transform: translateX(-100%); /* Slide primary image to the left */
        }

        .tp-product-thumb-2:hover img.secondary {
            transform: translateX(0); /* Slide secondary image to the center */
        }
        .tp-product-thumb-2 {
            height: 280px; /* Hoặc chiều cao phù hợp với design */
            overflow: hidden;
        }
    </style>
        <a href="{{ $product->url }}">
            {{ RvMedia::image($product->image, $product->name, $style === 2 ? 'thumb' : 'medium', true) }}
            @if($product->images && count($product->images) > 1)
                {{ RvMedia::image($product->images[1], $product->name, $style === 2 ? 'thumb' : 'medium', true, ['class' => 'product-image secondary']) }}
            @else
                {{ RvMedia::image($product->image, $product->name, $style === 2 ? 'thumb' : 'medium', true, ['class' => 'product-image secondary']) }}
            @endif
        </a>
        @include(Theme::getThemeNamespace('views.ecommerce.includes.product.badges'))

        @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-2.actions'))
    </div>
    <div class="tp-product-content-2 pt-15">
        {!! apply_filters('ecommerce_before_product_item_content_renderer', null, $product) !!}

        @if (is_plugin_active('marketplace') && $product->store?->id)
            <div class="tp-product-tag-2">
                <a href="{{ $product->store->url }}">{{ $product->store->name }}</a>
            </div>
        @endif

        <h3 class="tp-product-title-2 text-truncate">
            <a href="{{ $product->url }}" title="{{ $product->name }}">{{ $product->name }}</a>
        </h3>

        <div @class(['mt-2 tp-product-price-review' => theme_option('product_listing_review_style', 'default') !== 'default' && EcommerceHelper::isReviewEnabled() && ($product->reviews_avg || theme_option('ecommerce_hide_rating_star_when_is_zero', 'no') === 'no')])>
            @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-2.rating'))

            @include(Theme::getThemeNamespace('views.ecommerce.includes.product.style-2.price'))
        </div>

        {!! apply_filters('ecommerce_after_product_item_content_renderer', null, $product) !!}
    </div>
</div>
