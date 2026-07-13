<div
    x-data="{
        primary: '#D4A017',
        primaryHover: '#B8890F',
        secondary: '#0B2B5C',
        footerBg: '#071A33',
        footerAccent: '#ED1C24',
        init() {
            this.sync();
            this.$watch(() => JSON.stringify($wire.data), () => this.sync());
        },
        sync() {
            this.primary = $wire.data?.primary_color || '#D4A017';
            this.primaryHover = $wire.data?.primary_color_hover || '#B8890F';
            this.secondary = $wire.data?.secondary_color || '#0B2B5C';
            this.footerBg = $wire.data?.footer_bg_color || '#071A33';
            this.footerAccent = $wire.data?.footer_accent_color || '#ED1C24';
        }
    }"
    style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.06);"
>
    {{-- Navbar --}}
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; background: #fff; border-bottom: 1px solid #f1f5f9;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 28px; height: 28px; border-radius: 6px; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 14px; height: 14px; color: #94a3b8;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <span style="font-size: 12px; font-weight: 800;" :style="'color: ' + secondary">KIMMEX</span>
        </div>
        <div style="display: flex; align-items: center; gap: 14px;">
            <span style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;" :style="'color: ' + secondary + '99'">About</span>
            <span style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;" :style="'color: ' + secondary + '99'">Services</span>
            <span style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;" :style="'color: ' + secondary + '99'">Projects</span>
            <span style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;" :style="'color: ' + secondary + '99'">Contact</span>
        </div>
    </div>

    {{-- Hero --}}
    <div style="padding: 24px 20px;" :style="'background: ' + secondary">
        <p style="font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.25em; margin-bottom: 6px;" :style="'color: ' + primary">Featured Project</p>
        <h2 style="font-size: 16px; font-weight: 900; color: #fff; text-transform: uppercase; margin-bottom: 6px; line-height: 1.1;">Building Cambodia's Future</h2>
        <p style="font-size: 10px; color: rgba(255,255,255,0.55); margin-bottom: 14px; max-width: 280px; line-height: 1.5;">Over 25 years of excellence in construction and engineering solutions.</p>
        <div style="display: flex; gap: 8px;">
            <span style="font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; padding: 6px 12px; border-radius: 4px; color: #fff;" :style="'background: ' + primary">View Project</span>
            <span style="font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.15em; padding: 6px 12px; border-radius: 4px; color: #fff; border: 1.5px solid rgba(255,255,255,0.3);">Contact Us</span>
        </div>
    </div>

    {{-- Content --}}
    <div style="padding: 16px 20px; background: #fff;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
            <div style="width: 24px; height: 2px;" :style="'background: ' + primary"></div>
            <span style="font-size: 8px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.2em;" :style="'color: ' + primary">Our Services</span>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px;">
            <div style="padding: 8px; border-radius: 6px; border: 1px solid #f1f5f9; text-align: center;">
                <div style="width: 24px; height: 24px; margin: 0 auto 4px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" :style="'background: ' + primary + '1a'">
                    <svg style="width: 12px; height: 12px;" :style="'color: ' + primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                </div>
                <span style="font-size: 8px; font-weight: 700;" :style="'color: ' + secondary">Construction</span>
            </div>
            <div style="padding: 8px; border-radius: 6px; border: 1px solid #f1f5f9; text-align: center;">
                <div style="width: 24px; height: 24px; margin: 0 auto 4px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" :style="'background: ' + primary + '1a'">
                    <svg style="width: 12px; height: 12px;" :style="'color: ' + primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <span style="font-size: 8px; font-weight: 700;" :style="'color: ' + secondary">Engineering</span>
            </div>
            <div style="padding: 8px; border-radius: 6px; border: 1px solid #f1f5f9; text-align: center;">
                <div style="width: 24px; height: 24px; margin: 0 auto 4px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" :style="'background: ' + primary + '1a'">
                    <svg style="width: 12px; height: 12px;" :style="'color: ' + primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                </div>
                <span style="font-size: 8px; font-weight: 700;" :style="'color: ' + secondary">MEP Systems</span>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div style="padding: 14px 20px; position: relative;" :style="'background: ' + footerBg">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 2px;" :style="'background: linear-gradient(90deg, ' + footerAccent + ', transparent 50%)'"></div>
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span style="font-size: 10px; font-weight: 700; color: rgba(255,255,255,0.85);">KIMMEX</span>
                <p style="font-size: 8px; color: rgba(255,255,255,0.4); margin-top: 2px;">Building Cambodia's Future</p>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 8px; color: rgba(255,255,255,0.5);">About</span>
                <span style="font-size: 8px;" :style="'color: ' + footerAccent">Services</span>
                <span style="font-size: 8px; color: rgba(255,255,255,0.5);">Contact</span>
            </div>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.08);">
            <span style="font-size: 7px; color: rgba(255,255,255,0.3);">© 2026 KIMMEX. All rights reserved.</span>
            <div style="display: flex; gap: 4px;">
                <div style="width: 16px; height: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.08);">
                    <svg style="width: 8px; height: 8px; color: rgba(255,255,255,0.6);" fill="currentColor" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/></svg>
                </div>
                <div style="width: 16px; height: 16px; border-radius: 4px; display: flex; align-items: center; justify-content: center;" :style="'background: ' + footerAccent">
                    <svg style="width: 8px; height: 8px; color: #fff;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </div>
            </div>
        </div>
    </div>
</div>
