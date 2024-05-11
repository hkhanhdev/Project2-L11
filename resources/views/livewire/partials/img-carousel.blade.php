<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="carousel w-10/12">
    <div id="slide1" class="carousel-item relative w-full">
        <img src="https://ik.imagekit.io/0398925392/img_project2/slider_6.jpg_1715318781129?updatedAt=1715400565722" class="w-full" />
        <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
            <a href="#slide4" class="btn btn-circle">❮</a>
            <a href="#slide2" class="btn btn-circle">❯</a>
        </div>
    </div>
    <div id="slide2" class="carousel-item relative w-full">
        <img src="https://ik.imagekit.io/0398925392/img_project2/slider_3.jpg_1715318781129?updatedAt=1715400587413" class="w-full" />
        <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
            <a href="#slide1" class="btn btn-circle">❮</a>
            <a href="#slide3" class="btn btn-circle">❯</a>
        </div>
    </div>
    <div id="slide3" class="carousel-item relative w-full">
        <img src="https://ik.imagekit.io/0398925392/img_project2/slider_2.jpg_1715318781129?updatedAt=1715400844555" class="w-full" />
        <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
            <a href="#slide2" class="btn btn-circle">❮</a>
            <a href="#slide4" class="btn btn-circle">❯</a>
        </div>
    </div>
    <div id="slide4" class="carousel-item relative w-full">
        <img src="https://ik.imagekit.io/0398925392/img_project2/slider_7.jpg_1715318781129?updatedAt=1715400854290" class="w-full" />
        <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
            <a href="#slide3" class="btn btn-circle">❮</a>
            <a href="#slide1" class="btn btn-circle">❯</a>
        </div>
    </div>
</div>
