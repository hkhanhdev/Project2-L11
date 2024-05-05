<?php

use Livewire\Volt\Component;

new class extends Component {
    //

    public function brands()
    {
        return \App\Models\Brands::all('name');
    }
    public function with():array
    {
        return [
            'all_brands' => $this->brands()
        ];
    }
}; ?>

{{--<div class="flex justify-center">--}}
    <div class="h-60 flex flex-col justify-center items-center">
        <span class="text-3xl font-semibold mb-10">Brands</span>

        <div class="w-full flex items-center justify-center">
            <div class="w-11/12 rounded-xl">
                <div class="w-full overflow-visible">
                    <script>
                        window.carousel = function () {
                            return {
                                container: null,
                                prev: null,
                                next: null,
                                init() {
                                    this.container = this.$refs.container

                                    this.update();

                                    this.container.addEventListener('scroll', this.update.bind(this), {passive: true});
                                },
                                update() {
                                    const rect = this.container.getBoundingClientRect();

                                    const visibleElements = Array.from(this.container.children).filter((child) => {
                                        const childRect = child.getBoundingClientRect()

                                        return childRect.left >= rect.left && childRect.right <= rect.right;
                                    });

                                    if (visibleElements.length > 0) {
                                        this.prev = this.getPrevElement(visibleElements);
                                        this.next = this.getNextElement(visibleElements);
                                    }
                                },
                                getPrevElement(list) {
                                    const sibling = list[0].previousElementSibling;

                                    if (sibling instanceof HTMLElement) {
                                        return sibling;
                                    }

                                    return null;
                                },
                                getNextElement(list) {
                                    const sibling = list[list.length - 1].nextElementSibling;

                                    if (sibling instanceof HTMLElement) {
                                        return sibling;
                                    }

                                    return null;
                                },
                                scrollTo(element) {
                                    const current = this.container;

                                    if (!current || !element) return;

                                    const nextScrollPosition =
                                        element.offsetLeft +
                                        element.getBoundingClientRect().width / 2 -
                                        current.getBoundingClientRect().width / 2;

                                    current.scroll({
                                        left: nextScrollPosition,
                                        behavior: 'smooth',
                                    });
                                }
                            };
                        }
                    </script>
                    <style>
                        .scroll-snap-x {
                            scroll-snap-type: x mandatory;
                        }

                        .snap-center {
                            scroll-snap-align: center;
                        }

                        .no-scrollbar::-webkit-scrollbar {
                            display: none;
                        }

                        .no-scrollbar {
                            -ms-overflow-style: none;
                            scrollbar-width: none;
                        }
                    </style>

                    <div class="flex mx-auto items-center">
                        <div x-data="carousel()" x-init="init()" class="relative overflow-hidden group">
                            <div x-ref="container" class="md:-ml-4 md:flex md:overflow-x-scroll scroll-snap-x md:space-x-4 space-y-4 md:space-y-0 no-scrollbar">
                                @foreach($all_brands as $brand)
                                    <div class="ml-4 flex-auto flex-grow-0 flex-shrink-0 w-64 rounded-lg bg-gray-100 items-center justify-center snap-center overflow-hidden shadow-2xl">
                                        <div><img class="w-full h-44" src="https://images-platform.99static.com//JjFZHYSeAsHvOC1byfDEbXA1Ptg=/147x137:799x789/fit-in/590x590/99designs-contests-attachments/106/106969/attachment_106969645" alt="Logo"></div>
                                        <div class="py-2 flex justify-center">
                                            <div class="text-lg font-medium"><span class="hover:text-green-500 duration-200">{{$brand->brand_name}}</span></div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div @click="scrollTo(prev)" x-show="prev !== null"
                                 class="hidden md:block absolute top-1/2 left-0 bg-white p-2 rounded-full transition-transform ease-in-out transform -translate-x-full -translate-y-1/2 group-hover:translate-x-0 cursor-pointer">
                                <div>&lt;</div>
                            </div>
                            <div @click="scrollTo(next)" x-show="next !== null"
                                 class="hidden md:block absolute top-1/2 right-0 bg-white p-2 rounded-full transition-transform ease-in-out transform translate-x-full -translate-y-1/2 group-hover:translate-x-0 cursor-pointer">
                                <div>&gt;</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
{{--</div>--}}
