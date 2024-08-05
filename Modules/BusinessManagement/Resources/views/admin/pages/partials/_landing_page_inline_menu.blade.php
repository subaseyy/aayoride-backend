<div class="mb-3">
    <ul class="nav d-inline-flex nav--tabs p-1 rounded bg-white">
        <li class="nav-item">
            <a href="{{ route('admin.business.pages-media.landing-page.intro-section.index') }}" class="text-capitalize nav-link
                {{ Request::is('admin/business/pages-media/landing-page/intro-section') ? 'active' : '' }}
            ">{{ translate('intro_section') }}</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.business.pages-media.landing-page.our-solutions.index') }}" class="text-capitalize nav-link
                {{ Request::is('admin/business/pages-media/landing-page/our-solutions*') ? 'active' : '' }}
            ">{{ translate('Our_Solutions') }}</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.business.pages-media.landing-page.business-statistics.index') }}" class="text-capitalize nav-link
                {{ Request::is('admin/business/pages-media/landing-page/business-statistics') ? 'active' : '' }}
            ">{{ translate('business_statistics') }}</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.business.pages-media.landing-page.earn-money.index') }}" class="text-capitalize nav-link
                {{ Request::is('admin/business/pages-media/landing-page/earn-money') ? 'active' : '' }}
            ">{{ translate('earn_money') }}</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.business.pages-media.landing-page.testimonial.index') }}" class="text-capitalize nav-link
                {{ Request::is('admin/business/pages-media/landing-page/testimonial*') ? 'active' : '' }}
            ">{{ translate('testimonial') }}</a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.business.pages-media.landing-page.cta.index') }}" class="text-capitalize nav-link
                {{ Request::is('admin/business/pages-media/landing-page/cta') ? 'active' : '' }}
            ">{{ translate('CTA') }}</a>
        </li>
    </ul>
</div>
