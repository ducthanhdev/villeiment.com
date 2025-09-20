  <!-- Section 1: Slider -->
  <section class="bg-black text-white h-screen">
    <div class="slider-container">
      <!-- Nút điều hướng giữa -->
      <div class="nav-center">
        <button id="prevBtn" class="btn-circle">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 50">
            <polygon points="28.2,13.5 28.2,29.5 99,29.5 99,36.5 1,36.5"></polygon>
          </svg>
        </button>
        <button id="nextBtn" class="btn-circle">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 50">
            <polygon points="71.8,13.5 71.8,29.5 1,29.5 1,36.5 99,36.5"></polygon>
          </svg>
        </button>
      </div>

      <!-- Cột trái -->
      <div class="column-half">
        <img id="leftImg" src="/villeimentimg/ShadowTeeMatTruoc.jpg" class="w-full h-full object-cover" alt="Fashion Left"/>
        <div class="social-links">
          <a class="vertical-text" href="#">Facebook</a>
          <a class="vertical-text" href="#">Instagram</a>
          <a class="vertical-text" href="#">Twitter</a>
          <a class="vertical-text" href="#">LinkedIn</a>
        </div>
        <div class="bottom-text">
          <p class="text-sm tracking-widest">REFRESHING DESIGN</p>
          <h2 id="leftTitle" class="text-3xl font-bold mt-2 hover:text-[#bb9d7b]">Urban Style</h2>
        </div>
      </div>

      <!-- Cột phải -->
      <div class="column-half">
        <img id="rightImg" src="/villeimentimg/ShadowTeeMatSau.jpg" class="w-full h-full object-cover" alt="Fashion Right"/>
        <div class="bottom-text">
          <p class="text-sm tracking-widest">FINE FABRIC</p>
          <h2 id="rightTitle" class="text-3xl font-bold mt-2 hover:text-[#bb9d7b]">Classic Suit</h2>
        </div>
        <div class="page-numbers">
          <span class="page-num active" data-index="0">01</span>
          <span class="page-num" data-index="1">02</span>
          <span class="page-num" data-index="2">03</span>
        </div>
      </div>

      <!-- Scroll down button -->
      <div class="elementor-element elementor-element-65161da elementor-widget__width-auto elementor-absolute elementor-hidden-mobile_extra elementor-hidden-mobile wdt-scroll-icon elementor-view-default elementor-widget elementor-widget-icon" data-id="65161da" data-element_type="widget" data-settings="{&quot;_position&quot;:&quot;absolute&quot;,&quot;wdt_animation_effect&quot;:&quot;none&quot;}" data-widget_type="icon.default">
          <div class="elementor-widget-container">
              <div class="elementor-icon-wrapper">
                  <a class="elementor-icon" href="#section-two">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 120 120" style="enable-background:new 0 0 120 120; fill: white;" xml:space="preserve">
                          <style type="text/css">
                              svg .scrolldown-ico {
                                  -webkit-animation: scrollDown 10s infinite linear;
                                  animation: scrollDown 10s infinite linear;
                                  transform-origin: center;
                                  animation-play-state: running;
                              }
                              svg:hover .scrolldown-ico {
                                  animation-play-state: paused;
                              }

                              @-webkit-keyframes scrollDown {
                                  to {
                                      transform: rotate(360deg);
                                  }
                              }
                              @keyframes scrollDown {
                                  to {
                                      transform: rotate(360deg);
                                  }
                              }
                              /* Added to ensure all paths are white */
                          </style>
                          <path d="M60,95.2c-19.4,0-35.2-15.8-35.2-35.2c0-19.4,15.8-35.2,35.2-35.2S95.2,40.6,95.2,60C95.2,79.4,79.4,95.2,60,95.2z M60,26.8 c-18.3,0-33.2,14.9-33.2,33.2c0,18.3,14.9,33.2,33.2,33.2c18.3,0,33.2-14.9,33.2-33.2C93.2,41.7,78.3,26.8,60,26.8z"></path>
                          <path class="scrolldown-ico" d="M107.6,54.4l-0.2-1.7l10.8-1.6l0.2,1.7L107.6,54.4z M106.7,49.1l10.8-2.2l0.4,1.7l-10.8,2.2L106.7,49.1z M110.3,46.4 c-2.2,0.7-4.5-0.5-5.2-2.6s0.5-4.5,2.6-5.2c2.2-0.7,4.5,0.5,5.2,2.5c0.8,2-0.2,4.3-2.3,5.2C110.6,46.2,110.5,46.2,110.3,46.4 L110.3,46.4z M111.5,41.6c-0.5-1.4-1.9-1.8-3.3-1.3c-1.3,0.5-2.3,1.7-1.8,3.1c0.5,1.4,1.9,1.8,3.3,1.3C111.1,44.2,112,43,111.5,41.6 L111.5,41.6z M109.1,36c-0.4-0.7-1.1-1.2-1.9-1.3l-4.8,2.5l-0.8-1.6l7.1-3.7l0.8,1.6l-1.1,0.6c1,0.1,1.9,0.7,2.4,1.7l-1.6,0.8 C109.2,36.3,109.1,36.1,109.1,36z M105,30.4c0.2-0.7,0.1-1.4-0.4-2c-1-1.1-2.6-1.2-3.7-0.2c-1,0.8-1.2,2.3-0.6,3.4 c0.4,0.6,1.1,1,1.8,1v1.4c-1.2,0-2.3-0.6-2.9-1.7c-1.3-1.9-0.8-4.5,1.1-5.8c1.8-1.3,4.3-0.8,5.8,1c0.7,1,1,2.2,0.6,3.3L105,30.4z M101.1,21.1c0-1.2-0.6-2.3-1.4-3.1c-0.8-0.8-1.7-0.8-2.3-0.4c-0.6,0.6,0,1.6,0.7,2.8c1,1.6,2.2,3.5,0.5,5.2 c-1.3,1.3-3.3,1.6-5.4-0.5c-1.2-1.1-2-2.6-2-4.3l1.8-0.4c0,1.3,0.5,2.6,1.4,3.5c1.1,1.1,2.2,1,2.6,0.5c0.7-0.7,0-1.7-0.7-2.9 c-1-1.6-2.2-3.4-0.5-5.1c1.2-1.3,3.4-1.2,5.2,0.5c1.1,1,1.8,2.4,1.9,4L101.1,21.1z M90.5,10.7l-1.9-0.1l1,1.6l-0.8,0.5L87.8,11 L87,12.7l-0.8-0.5l1.2-1.6l-1.8,0.1v-1l1.8,0.1l-1-1.6l0.7-0.5l0.8,1.7l0.8-1.7l0.8,0.5l-1,1.6l1.8-0.1L90.5,10.7z M96.2,90.8 l5.7,5.7l-1.2,1.2l-0.7-0.7c0.1,1.1-0.4,2.2-1.1,2.9c-1.2,1.2-2.5,1.2-3.6,0l-4-4l1.2-1.2l3.5,3.5c0.5,0.6,1.3,0.8,1.9,0.4 c0.1-0.1,0.2-0.2,0.4-0.4c0.6-0.6,0.8-1.4,0.7-2.2l-4-4L96.2,90.8z M91.4,108.4h-1.8l1,1.6l-0.8,0.5l-0.8-1.7l-0.8,1.7l-0.8-0.5 l1-1.6l-1.7,0.1v-1l1.8,0.1l-1.1-1.6l0.8-0.5l0.8,1.7l0.8-1.7l0.8,0.5l-1,1.6l1.8-0.1L91.4,108.4z M76.3,14.2l-1.7-0.5l1.3-4.8 c0.4-0.7,0-1.6-0.7-1.9c-0.1,0-0.2,0-0.4,0c-0.8-0.2-1.6,0-2.3,0.5l-1.4,5.3l-1.7-0.5l2.2-7.7l1.7,0.5L73,6.2c0.8-0.6,2-0.7,3-0.5 c1.7,0.5,2.3,1.6,1.8,3.1L76.3,14.2z M64.1,11.7l-1.6-5.9l-2.2,5.8l-1.8-0.1l-2-8.1l1.8,0.1l1.4,5.9l2.2-5.7l1.6,0.1l1.7,5.9 l1.9-5.7l1.8,0.1l-3,7.6L64.1,11.7z M72.6,105.4c1.6-0.5,3.4-0.4,4.8,0.5l-0.6,1.7c-1.1-0.7-2.5-0.8-3.7-0.6c-1.6,0.4-1.9,1.3-1.7,2 c0.2,1,1.4,1,2.9,1c1.8,0,4-0.1,4.7,2.2c0.5,1.8-0.7,3.5-3,4.2c-1.4,0.5-3,0.4-4.3-0.4l0.7-1.7c1.1,0.6,2.3,0.7,3.4,0.4 c1.1-0.2,1.7-1,1.4-1.8c-0.2-0.8-1.3-0.8-2.8-0.7c-1.9,0-4.1,0-4.7-2.3C68.9,108,69.8,106.1,72.6,105.4L72.6,105.4z M64.1,115.2 c-1.2,0.1-2.4-0.2-3.1-1.2l1.1-1.2c0.5,0.6,1.2,0.8,1.9,0.7c1.4-0.2,2.4-1.7,2.2-3.1c-0.2-1.2-1.3-2.2-2.5-2.2 c-0.7,0-1.3,0.5-1.7,1.1l-1.2-1c0.6-1,1.7-1.7,2.9-1.7c2.3-0.1,4.3,1.6,4.5,3.9C67.9,113.1,66.4,115,64.1,115.2L64.1,115.2z M51.8,12.5c-2.2,0.5-4.2-1-4.7-3.1c0-0.1,0-0.2,0-0.4c-0.5-2.2,1.1-4.3,3.3-4.8s4.3,1.1,4.8,3.3v0.1c0.5,2.2-0.8,4.3-3,4.8 C52.1,12.4,51.9,12.4,51.8,12.5L51.8,12.5z M53.4,8c-0.2-1.3-1.3-2.4-2.8-2.2c-1.4,0.2-2,1.6-1.8,3c0.4,1.3,1.3,2.4,2.8,2.2 C53,10.7,53.6,9.4,53.4,8L53.4,8z M58.8,115.2l-1.7-0.1l0.1-1.2c-0.6,0.8-1.7,1.2-2.6,1.2l0.1-1.7c0.1,0,0.4,0.1,0.5,0.1 c0.8,0,1.6-0.4,2-1l0.2-5.4l1.7,0.1L58.8,115.2z M41.3,15.3l-3.7,1.6L33.4,6.6l3.7-1.6c3.1-1.3,6.1-0.1,7.5,2.9 C45.8,11.1,44.6,14.1,41.3,15.3z M42.8,8.8c-0.6-1.8-2.6-2.9-4.5-2.3c-0.1,0-0.2,0.1-0.5,0.1l-2,0.8l2.9,7l2-0.8 c1.9-0.7,2.9-2.8,2.2-4.6C42.9,9,42.8,8.9,42.8,8.8L42.8,8.8z M50.4,106.1c2.3,0.5,3.7,2.6,3.4,4.9c-0.4,2.3-2.6,3.7-4.9,3.4 c-2.3-0.5-3.7-2.6-3.4-4.9c0,0,0,0,0-0.1c0.4-2.2,2.4-3.7,4.6-3.4C50.2,106.1,50.4,106.1,50.4,106.1z M49,112.8 c1.4,0.2,2.5-0.7,2.8-2.2c0.2-1.4-0.2-2.8-1.7-3s-2.5,0.7-2.8,2.2C47,111.1,47.6,112.5,49,112.8L49,112.8z M39.8,114.6l3.6-10.4 l1.7,0.6l-3.6,10.4L39.8,114.6z M24.1,12.8l1.4-1.1l6.6,8.8l-1.4,1.1L24.1,12.8z M28,23.9l-7.2-8.3l1.3-1.1l7.2,8.3L28,23.9z M23.8,22.2c1.7,1.4,1.9,4.1,0.5,5.8c-1.4,1.7-4.1,1.9-5.8,0.5l-0.1-0.1c-1.7-1.4-1.9-4.1-0.5-5.8c1.4-1.7,4.1-1.9,5.8-0.5 C23.6,22,23.8,22.2,23.8,22.2z M22.6,23.5c-1.1-1-2.5-1.1-3.5,0S18.5,26,19.5,27s2.5,1.1,3.5,0C24,25.9,23.6,24.5,22.6,23.5 L22.6,23.5z M15.4,28.4c-0.4,0.7-0.5,1.6-0.2,2.3l4.6,2.9l-1,1.4l-6.7-4.3l1-1.4l1,0.6c-0.4-1-0.2-2,0.2-2.9l1.4,1 C15.6,28.1,15.4,28.2,15.4,28.4L15.4,28.4z M10.9,35.9c-0.6,1.2,0,2.6,1.2,3.3c0.1,0,0.1,0.1,0.2,0.1c1.2,0.7,2.6,0.2,3.4-0.8 c0-0.1,0.1-0.1,0.1-0.2c0.4-0.6,0.2-1.4-0.1-2l1.4-0.6c0.6,1.1,0.6,2.3,0,3.4c-0.8,2-3.3,3-5.3,2.2c-0.1,0-0.2-0.1-0.2-0.1 c-2-0.8-3-3.1-2.2-5.2c0-0.1,0.1-0.2,0.1-0.2c0.5-1.1,1.4-1.9,2.5-2.2l0.5,1.4C11.8,34.8,11.1,35.2,10.9,35.9L10.9,35.9z M11.6,42.6 c1.8,0.5,3,2,2.3,4.9c-0.4,1.7-1.3,3-2.8,3.9L9.9,50c1.2-0.6,2-1.7,2.3-3c0.4-1.6-0.2-2.3-1-2.5c-1-0.2-1.6,0.8-2.2,2 c-0.8,1.7-1.9,3.5-4.2,3c-1.7-0.4-2.6-2.3-2-4.6c0.4-1.4,1.2-2.8,2.5-3.6l1.1,1.4c-1,0.6-1.7,1.6-1.9,2.8c-0.2,1.1,0.1,1.9,0.8,2.2 c0.8,0.2,1.3-0.8,2-1.9C8.2,44,9.3,42,11.6,42.6L11.6,42.6z M5.1,57.6l-1,1.6L5.9,59v1l-1.8-0.1l1,1.6l-0.8,0.5l-0.8-1.7l-0.8,1.7 l-0.8-0.5l1-1.6L1,60v-1l1.8,0.1l-1-1.6l0.8-0.5l0.8,1.7l0.8-1.7L5.1,57.6z M7.4,69l5.4-1.3l0.4,1.7l-4.8,1.2 c-1.2,0.2-1.4,1-1.2,1.9c0.2,0.7,0.8,1.4,1.6,1.7l5.4-1.3l0.4,1.7l-7.7,1.9l-0.4-1.7l1.1-0.2c-1-0.5-1.7-1.3-1.9-2.4 C5.1,70.5,5.7,69.4,7.4,69z M16.5,79.5l-4.3,4.3l6-1l0.8,1.6l-6,5.8l-0.8-1.6l4.5-4.1l-6,1l-0.7-1.4l4.3-4.3l-5.9,1.1l-0.8-1.6 l8.2-1.4L16.5,79.5z M17.4,89.2c1.7-1.4,4.3-1.3,5.8,0.5c1.4,1.7,1.3,4.3-0.5,5.8c0,0-0.1,0-0.1,0.1c-1.7,1.4-4.3,1.3-5.8-0.5 C15.3,93.3,15.6,90.8,17.4,89.2L17.4,89.2z M18,94.1c1,1.1,2.4,1,3.5,0.1c1.1-1,1.6-2.3,0.6-3.5c-1-1.1-2.4-1-3.5-0.1 C17.5,91.5,17,93,18,94.1L18,94.1z M31,97.4l3.3,2.5l-6.7,8.7l-3.3-2.5c-2.6-2.2-3.1-5.3-1.1-7.9C25.2,95.5,28.2,95.3,31,97.4 L31,97.4z M25.2,104.8l1.7,1.3l4.7-6l-1.7-1.3c-1.6-1.3-3.7-1.1-5.1,0.4c-0.1,0.1-0.1,0.2-0.2,0.2c-1.3,1.4-1.2,3.7,0.2,5.1 C25,104.5,25.1,104.6,25.2,104.8z M41.6,103.6l-4.5,10.1l-1.6-0.7l4.5-10.1L41.6,103.6z M99.5,86.6l5.9,1.6l-4-4.7l1-1.6l8.1,2.2 l-1,1.6l-5.8-1.7l3.9,4.7l-0.8,1.3l-5.9-1.4l4,4.6l-1,1.6l-5.4-6.4L99.5,86.6z M107.4,80.8c-2.2-0.8-3.1-3.1-2.4-5.3 c0.8-2.2,3.1-3.1,5.3-2.4c2,0.8,3.1,3.1,2.4,5.2c-0.7,2-2.9,3.3-5.1,2.5C107.6,80.9,107.6,80.8,107.4,80.8L107.4,80.8z M111.3,77.8 c0.5-1.3-0.4-2.5-1.7-3c-1.3-0.5-2.8-0.1-3.3,1.2c-0.5,1.4,0.4,2.6,1.7,3.1C109.4,79.6,110.8,79.2,111.3,77.8L111.3,77.8z M107.6,65 l0.5-4.1l11,1.4l-0.5,4.1c-0.5,3.4-3,5.4-6.3,5.1C109,71.2,107.1,68.4,107.6,65z M116.7,66.2l0.2-2.2l-7.6-1l-0.2,2.2 c-0.4,1.9,1,3.9,2.9,4.2c0.1,0,0.2,0,0.4,0c1.9,0.4,3.9-0.8,4.2-2.8C116.7,66.5,116.7,66.4,116.7,66.2L116.7,66.2z"></path>
                          <path d="M53.5,49.1h13.1c0.9,0,1.8,0.2,2.5,0.7c0.7,0.5,1.3,1.1,1.7,1.9c0.4,0.7,0.7,1.6,0.7,2.5s-0.2,1.9-0.7,2.7l-6.6,11.5 c-0.5,0.8-1.1,1.5-1.9,1.9c-0.6,0.3-1.4,0.6-2.2,0.6c-0.8,0-1.6-0.2-2.4-0.7c-0.7-0.4-1.4-1.1-1.9-1.9l-6.6-11.5 c-0.5-0.8-0.7-1.7-0.7-2.7c0-0.9,0.2-1.7,0.7-2.5c0.4-0.7,1.1-1.4,1.7-1.9C51.7,49.4,52.6,49.1,53.5,49.1z"></path>
                      </svg>
                  </a>
              </div>
          </div>
      </div>
    </div>
  </section>
  <!-- Section 2: Hình ảnh -->
<section class="tp-slider-area p-relative z-index-1">
    <div class="tp-slider-active-2 swiper-container @if ($shortcode->animation_enabled == 'no') tp-slider-no-animation @endif"
         data-loop="{{ $shortcode->is_loop == 'yes' }}"
         data-autoplay="{{ $shortcode->is_autoplay == 'yes' }}"
         data-autoplay-speed="{{ in_array($shortcode->autoplay_speed, [2000, 3000, 4000, 5000, 6000, 7000, 8000, 9000, 10000]) ? $shortcode->autoplay_speed : 5000 }}"
    >
        <div class="swiper-wrapper">
            @foreach ($sliders as $slider)
                @php
                    $title = $slider->title;
                    $description = $slider->description;
                @endphp

                <div class="tp-slider-item-2 tp-slider-height-2 p-relative swiper-slide" style="background: linear-gradient(135deg, #1a1a1a, #2d2d2d);" id="section-two">
                    <div class="tp-slider-2-shape">
                        <!-- @if($shape = $shortcode->shape_1)
                            {{ RvMedia::image($shape, $slider->title, attributes: ['class' => 'tp-slider-2-shape-1', 'style' => 'opacity: 0.3;']) }}
                        @endif -->
                    </div>
                    @if($title || $description)
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <div class="tp-slider-content-2">
                                        @if($description)
                                            <span class="tp-slider-subtitle text-white" @if($fontFamily = $shortcode->font_family_of_description) style="--tp-ff-oregano: '{{ $fontFamily }}'" @endif>
                                                {!! BaseHelper::clean($description) !!}
                                            </span>
                                        @endif
                                        @if ($title)
                                            <h3 class="tp-slider-title-2 text-white" @style(["font-size: {$shortcode->title_font_size}px" => $shortcode->title_font_size])>
                                                {!! BaseHelper::clean($title) !!}
                                            </h3>
                                        @endif
                                        @if($buttonLabel = $slider->getMetaData('button_label', true))
                                            <div class="tp-slider-btn-2">
                                                <a href="{{ $slider->link }}" class="tp-btn tp-btn-border tp-btn-dark">
                                                    {!! BaseHelper::clean($buttonLabel) !!}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <div class="tp-slider-thumb-2-wrapper p-relative">
                                        <!-- <div class="tp-slider-thumb-2-shape">
                                            @if($shape = $shortcode->shape_2)
                                                {{ RvMedia::image($shape, $slider->title, attributes: ['class' => 'tp-slider-thumb-2-shape-1', 'style' => 'opacity: 0.2;']) }}
                                            @endif
                                            @if($shape = $shortcode->shape_3)
                                                {{ RvMedia::image($shape, $slider->title, attributes: ['class' => 'tp-slider-thumb-2-shape-1', 'style' => 'opacity: 0.2;']) }}
                                            @endif
                                        </div> -->
                                        <div class="tp-slider-thumb-2 text-end">
                                            <span class="tp-slider-thumb-2-gradient" style="background: linear-gradient(to right, rgba(0, 0, 0, 0.7), transparent);"></span>
                                            @php $slider->title = $title; @endphp
                                            @include(Theme::getThemeNamespace('partials.shortcodes.simple-slider.includes.image', ['slider' => $slider]))
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="tp-slider-thumb-2-wrapper p-relative">
                            <div class="tp-slider-thumb-2">
                                <span class="tp-slider-thumb-2-gradient" style="background: linear-gradient(to right, rgba(0, 0, 0, 0.7), transparent);"></span>
                                @include(Theme::getThemeNamespace('partials.shortcodes.simple-slider.includes.image', ['slider' => $slider]))
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="tp-swiper-dot tp-slider-2-dot"></div>
    </div>
    <style>
        .tp-slider-height-2 {
            min-height: 600px;
            position: relative;
            overflow: hidden;
        }

        .tp-slider-item-2 {
            color: #fff;
            padding: 50px 0;
        }

        .tp-slider-subtitle {
            font-size: 1.2rem;
            color: #bbb;
            letter-spacing: 1px;
            margin-bottom: 15px;
            display: block;
        }

        .tp-slider-title-2 {
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        .tp-btn-dark {
            background: #333;
            border-color: #555;
            color: #fff !important;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .tp-btn-dark:hover {
            background: #fff;
            color: #333 !important;
            border-color: #fff;
        }

        .tp-slider-thumb-2-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .tp-slider-2-dot {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .tp-slider-2-dot .swiper-pagination-bullet {
            opacity: 0.6;
            width: 12px;
            height: 12px;
            transition: all 0.3s ease;
        }

        .tp-slider-2-dot .swiper-pagination-bullet-active {
            background: #fff;
            opacity: 1;
            transform: scale(1.3);
        }

        @media (max-width: 768px) {
            .tp-slider-height-2 {
                min-height: 400px;
            }
            .tp-slider-content-2 {
                text-align: center;
            }
            .tp-slider-thumb-2 {
                text-align: center;
                margin-top: 20px;
            }
        }
    </style>
</section>
  <style>
    body {
      font-family: "Roboto", sans-serif;
      margin: 0;
    }

    section.bg-black {
      background-color: #000;
      color: #fff;
      height: 100vh;
      position: relative;
    }

    .slider-container {
      position: relative;
      display: flex;
      height: 100%;
    }

    /* Nút điều hướng giữa */
    .nav-center {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      display: flex;
      gap: 1rem;
      z-index: 10;
    }

    .btn-circle {
      width: 48px;
      height: 48px;
      border: 2px solid rgba(240, 240, 240, 0.8);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(240, 240, 240, 0.8);
      transition: all 0.3s;
      background: transparent;
      cursor: pointer;
    }

    .btn-circle:hover {
      color: #bb9d7b;
      border-color: #bb9d7b;
    }

    .btn-circle svg {
      width: 24px;
      height: 24px;
      fill: currentColor;
    }

    /* Cột trái & phải */
    .column-half {
      width: 50%;
      position: relative;
      overflow: hidden;
    }

    .column-half img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Chữ dọc bên trái */
    .vertical-text {
      writing-mode: vertical-rl;
      transform: rotate(180deg);
      font-size: 0.875rem;
      letter-spacing: 0.15em;
      color: #d1d5db;
      text-decoration: none;
      transition: color 0.3s;
    }

    .vertical-text:hover {
      color: #bb9d7b;
    }

    .social-links {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 2rem;
    }

    /* Tiêu đề dưới ảnh */
    .bottom-text {
      position: absolute;
      bottom: 2.5rem;
      left: 50%;
      transform: translateX(-50%);
      text-align: center;
    }

    .bottom-text p {
      font-size: 0.875rem;
      letter-spacing: 0.15em;
      margin: 0;
    }

    .bottom-text h2 {
      font-size: 1.875rem;
      font-weight: bold;
      margin-top: 0.5rem;
      transition: color 0.3s;
    }

    .bottom-text h2:hover {
      color: #bb9d7b;
    }

    /* Số trang bên phải */
    .page-numbers {
      position: absolute;
      top: 50%;
      right: 1.5rem;
      transform: translateY(-50%);
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 1rem;
      font-size: 1.125rem;
      font-weight: bold;
    }

    .page-num {
      cursor: pointer;
      transition: color 0.3s;
      color: #d1d5db;
    }

    .page-num.active {
      color: #bb9d7b;
    }

    /* Animation */
    .left-enter {
      animation: revealFromTop 1.5s ease forwards;
    }

    .right-enter {
      animation: revealFromBottom 1.5s ease forwards;
    }

    @keyframes revealFromTop {
      from { clip-path: polygon(0 0, 100% 0, 100% 0, 0 0); opacity: 0; }
      to { clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%); opacity: 1; }
    }

    @keyframes revealFromBottom {
      from { clip-path: polygon(0 100%, 100% 100%, 100% 100%, 0 100%); opacity: 0; }
      to { clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%); opacity: 1; }
    }

    /* Scroll down button */
    .wdt-scroll-icon {
      position: absolute;
      top: 92%;
      left: 50%;
      transform: translate(-50%, 0%);
      z-index: 50;
    }
    .elementor-icon-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .elementor-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 150px;
      height: 150px;
      color: #fff;
      text-decoration: none;
      border: 1px solid #fff;
      border-radius: 50%;
      transition: border-color 0.3s;
    }

    .elementor-icon:hover {
      border-color: #bb9d7b;
    }

    .scrolldown-ico {
      animation: scrollDown 10s infinite linear;
      transform-origin: center;
      animation-play-state: running;
      width: 24px;
      height: 24px;
    }

    .elementor-icon:hover .scrolldown-ico {
      animation-play-state: paused;
    }

    @keyframes scrollDown {
      to { transform: rotate(360deg); }
    }

    .scroll-text {
      font-size: 12px;
      letter-spacing: 2px;
      margin-top: 10px;
      color: rgba(255, 255, 255, 0.7);
    }

    html {
      scroll-behavior: smooth;
    }
  </style>
  <script>
    const slides = [
      { 
        leftImg: "/villeimentimg/ShadowTeeMatTruoc.jpg", 
        rightImg: "/villeimentimg/ShadowTeeMatSau.jpg", 
        leftTitle: "Urban Style", 
        rightTitle: "Classic Suit" 
      },
      { 
        leftImg: "/villeimentimg/DraculaShirt.jpg", 
        rightImg: "/villeimentimg/DraculaShirtSau.jpg", 
        leftTitle: "Enveloping Coats", 
        rightTitle: "Down Jackets" 
      },
      { 
        leftImg: "/villeimentimg/ShroudedStepFlared.jpg", 
        rightImg: "/villeimentimg/ShroudedStepFlaredSau.jpg", 
        leftTitle: "Winter Warmth", 
        rightTitle: "Street Fashion" 
      }
    ];

    let currentIndex = 0;

    const leftImg = document.getElementById("leftImg");
    const rightImg = document.getElementById("rightImg");
    const leftTitle = document.getElementById("leftTitle");
    const rightTitle = document.getElementById("rightTitle");
    const pageNums = document.querySelectorAll(".page-num");

    function updateSlide() {
      const slide = slides[currentIndex];

      // Xóa hiệu ứng cũ để reset
      leftImg.classList.remove("left-enter");
      rightImg.classList.remove("right-enter");
      void leftImg.offsetWidth; // trigger reflow
      void rightImg.offsetWidth;

      // Cập nhật ảnh và text
      leftImg.src = slide.leftImg;
      rightImg.src = slide.rightImg;
      leftTitle.textContent = slide.leftTitle;
      rightTitle.textContent = slide.rightTitle;

      // Thêm hiệu ứng mới
      leftImg.classList.add("left-enter");
      rightImg.classList.add("right-enter");

      // Cập nhật số trang
      pageNums.forEach((num, index) => {
        num.classList.toggle("active", index === currentIndex);
      });
    }

    document.getElementById("nextBtn").addEventListener("click", () => {
      currentIndex = (currentIndex + 1) % slides.length;
      updateSlide();
    });

    document.getElementById("prevBtn").addEventListener("click", () => {
      currentIndex = (currentIndex - 1 + slides.length) % slides.length;
      updateSlide();
    });

    pageNums.forEach(num => {
      num.addEventListener("click", () => {
        currentIndex = parseInt(num.dataset.index);
        updateSlide();
      });
    });

    updateSlide();
  </script>