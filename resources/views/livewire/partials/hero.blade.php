<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="hero min-h-svh" style="background-image: url(https://static.vecteezy.com/system/resources/previews/000/167/595/original/supplements-illustration-vector.jpg);">
    <div class="hero-overlay bg-opacity-60"></div>
    <div class="hero-content text-center text-neutral-content">
        <div class="max-w-md">
            <h1 class="mb-5 text-5xl font-bold">Hello there</h1>
            <p class="mb-5">Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem quasi. In deleniti eaque aut repudiandae et a id nisi.</p>
            <button class="btn btn-primary" onclick="scrollToSection('products')">Buy now</button>

        </div>
    </div>
</div>

<script>
    function scrollToSection(sectionId) {
        var section = document.getElementById(sectionId);
        if (section) {
            section.scrollIntoView({ behavior: 'smooth' });
        }
    }
</script>
