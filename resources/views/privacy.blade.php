@extends('layouts.app') {{-- Replace with your actual layout --}}

@section('title', 'Privacy Policy')

@section('content')
<div class="max-w-4xl mx-auto px-4 pt-28 pb-12 text-gray-800 dark:text-gray-200">
    <h1 class="text-3xl font-bold mb-4">Privacy Policy</h1>

    <div class="text-sm text-gray-500 dark:text-gray-400 mb-8 space-y-1">
        <p><strong>Website:</strong> KingsleyKhordpiano.com</p>
        <p><strong>Company:</strong> KingsleyKhordpiano, LLC</p>
        <p><strong>Effective Date:</strong> October 25, 2025</p>
        <p><strong>Contact Email:</strong> <a href="mailto:admin@kingsleykhordpiano.com" class="text-blue-600 hover:underline">admin@kingsleykhordpiano.com</a></p>
    </div>

    <div class="space-y-4 leading-relaxed">

        {{-- 1 --}}
        <h2 class="text-xl font-semibold mt-8">1. Introduction</h2>
        <p>KingsleyKhordpiano, LLC ("Kingsley Khord," "we," "us," or "our") operates KingsleyKhordpiano.com, an online piano learning platform that provides educational courses, lessons, digital products, and a community for musicians.</p>
        <p>This Privacy Policy explains how we collect, use, store, disclose, and protect personal information when you visit our website, create an account, purchase products, subscribe to a membership plan, or participate in our community.</p>
        <p>By using our website, you acknowledge that you have read this Privacy Policy. Where applicable law requires consent to particular data processing activities, we will obtain that consent separately.</p>

        {{-- 2 --}}
        <h2 class="text-xl font-semibold mt-8">2. Information We Collect</h2>
        <p>We collect information that is necessary to operate our platform, process transactions, provide educational services, maintain our community, and improve the user experience.</p>

        <h3 class="text-lg font-semibold">2.1 Personal Information</h3>
        <p>When you register for an account, we may collect:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Full name.</li>
            <li>Email address.</li>
            <li>Password, stored in a securely protected form.</li>
            <li>Country or region, where needed to provide services or meet legal requirements.</li>
        </ul>
        <p>You are responsible for providing accurate information and maintaining the security of your account credentials.</p>
        <p>We do not need your actual password in readable form to operate your account.</p>

        <h3 class="text-lg font-semibold">2.2 Payment and Transaction Information</h3>
        <p>When you purchase a membership subscription, course, MIDI file, plugin, or other digital product, we collect information associated with your transaction, such as:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>The product or subscription purchased.</li>
            <li>Payment amount and currency.</li>
            <li>Transaction date and status.</li>
            <li>Subscription plan and billing period.</li>
            <li>Relevant payment or refund records.</li>
        </ul>
        <p>Payments are processed through third-party payment providers, including Stripe and PayPal.</p>
        <p>These providers may collect and process payment information, such as card or account details, under their own privacy policies and applicable legal requirements. We do not intend to store complete payment card details on our own systems.</p>

        <h3 class="text-lg font-semibold">2.3 Community Content and Uploaded Files</h3>
        <p>Our platform includes a community where members may communicate and share permitted content.</p>
        <p>Depending on how you use these features, we may collect or process:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Profile pictures.</li>
            <li>Images and videos.</li>
            <li>Comments, messages, and other content you submit.</li>
            <li>Files and other materials you upload.</li>
            <li>Information associated with your community activity.</li>
        </ul>
        <p>Please be aware that content shared in community areas may be visible to other members. Do not upload confidential information or personal information belonging to someone else without appropriate permission.</p>

        <h3 class="text-lg font-semibold">2.4 Analytics and Usage Information</h3>
        <p>We collect analytics and usage statistics to understand how visitors and members interact with our website.</p>
        <p>Depending on the technologies we use, this information may include:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Pages and learning resources visited.</li>
            <li>Features used and interactions with the platform.</li>
            <li>General device, browser, and technical information.</li>
            <li>Session activity and website performance information.</li>
            <li>Referral information and approximate location information, where available.</li>
            <li>Error reports and diagnostic information.</li>
        </ul>
        <p>We may use cookies or similar technologies to support website functionality, understand usage patterns, and improve our services.</p>
        <p>Where required by applicable law, we will obtain consent before using non-essential cookies or similar tracking technologies.</p>

        <h3 class="text-lg font-semibold">2.5 Information You Provide to Support</h3>
        <p>If you contact our support team, we may collect your email address, account-related information, correspondence, screenshots, and other information you voluntarily provide to help us resolve your request.</p>
        <p>For refund requests, we may ask for a screenshot of the relevant transaction and the email address associated with your account.</p>
        <p>Please avoid including full payment card numbers, passwords, or other unnecessary sensitive information in support requests.</p>

        {{-- 3 --}}
        <h2 class="text-xl font-semibold mt-8">3. How We Use Your Information</h2>
        <p>We use personal information for the following purposes:</p>
        <ol class="list-decimal pl-6 space-y-1">
            <li><strong>Account creation and management:</strong> To register members, authenticate sign-ins, and maintain account access.</li>
            <li><strong>Educational services:</strong> To provide piano courses, lessons, practice resources, quizzes, master classes, workshops, and other membership benefits.</li>
            <li><strong>Subscription management:</strong> To administer Standard and Premium memberships, recurring billing, cancellations, and subscription access.</li>
            <li><strong>Digital product delivery:</strong> To provide purchased courses, MIDI files, plugins, and other digital resources.</li>
            <li><strong>Payment processing:</strong> To facilitate transactions and maintain purchase records.</li>
            <li><strong>Customer support:</strong> To respond to inquiries, troubleshoot problems, and resolve account or payment issues.</li>
            <li><strong>Refund processing:</strong> To investigate eligible refund requests and coordinate approved refunds.</li>
            <li><strong>Community operations:</strong> To enable member participation, manage uploaded content, and maintain the functionality of community features.</li>
            <li><strong>Analytics and improvement:</strong> To understand website usage, identify technical problems, and improve the performance and usefulness of our services.</li>
            <li><strong>Security and fraud prevention:</strong> To detect unauthorized access, investigate suspected hacking, and protect our platform and members.</li>
            <li><strong>Legal compliance:</strong> To comply with applicable laws, regulations, lawful requests, and legitimate legal obligations.</li>
        </ol>
        <p>We will not use personal information for purposes that are incompatible with this Privacy Policy unless we obtain any consent or establish another legal basis required by applicable law.</p>

        {{-- 4 --}}
        <h2 class="text-xl font-semibold mt-8">4. Legal Bases for Processing</h2>
        <p>Where privacy laws such as the European Union's General Data Protection Regulation (GDPR) apply, we process personal information under one or more of the following legal bases:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li><strong>Contract:</strong> Processing necessary to provide your account, membership, purchases, and requested services.</li>
            <li><strong>Legitimate interests:</strong> Processing necessary to secure, maintain, and improve our platform, provided those interests do not override your applicable rights.</li>
            <li><strong>Consent:</strong> Processing based on your consent, such as certain non-essential cookies or optional communications where consent is required.</li>
            <li><strong>Legal obligation:</strong> Processing necessary to comply with applicable laws.</li>
        </ul>
        <p>Where we rely on consent, you may withdraw it as permitted by law. Withdrawal does not affect the lawfulness of processing that occurred before withdrawal.</p>

        {{-- 5 --}}
        <h2 class="text-xl font-semibold mt-8">5. How We Share Information</h2>
        <p>We do not sell your personal information to third parties in exchange for money.</p>
        <p>We may disclose personal information to the following categories of recipients when necessary:</p>

        <h3 class="text-lg font-semibold">5.1 Payment Providers</h3>
        <p>Stripe and PayPal process payments and related transactions. Their processing of your information is subject to their own applicable terms and privacy policies.</p>

        <h3 class="text-lg font-semibold">5.2 Website and Technology Providers</h3>
        <p>We may use hosting providers, website infrastructure providers, analytics services, security tools, and other technology providers to operate the platform.</p>
        <p>These providers may process personal information on our behalf where necessary to provide their services.</p>

        <h3 class="text-lg font-semibold">5.3 Legal and Regulatory Authorities</h3>
        <p>We may disclose information when reasonably necessary to comply with applicable law, respond to lawful legal processes, protect our rights, investigate fraud, or address security threats.</p>

        <h3 class="text-lg font-semibold">5.4 Business Transfers</h3>
        <p>If KingsleyKhordpiano, LLC undergoes a merger, restructuring, financing, acquisition, or sale of some or all of its assets, personal information may be transferred as part of that transaction, subject to applicable legal requirements.</p>

        <h3 class="text-lg font-semibold">5.5 Community Members</h3>
        <p>Information and content that you voluntarily publish in community areas may be accessible to other members according to the functionality and visibility settings of those areas.</p>
        <p>We do not authorize third-party service providers to use personal information processed on our behalf for purposes unrelated to the services they provide, except as otherwise permitted by law.</p>

        {{-- 6 --}}
        <h2 class="text-xl font-semibold mt-8">6. International Data Transfers</h2>
        <p>Kingsley Khord is available to users in different countries. Your personal information may therefore be processed or stored in countries other than your country of residence.</p>
        <p>Where applicable law requires safeguards for international transfers, we will take appropriate steps to protect the information, which may include recognized contractual safeguards or another legally permitted transfer mechanism.</p>

        {{-- 7 --}}
        <h2 class="text-xl font-semibold mt-8">7. Data Retention</h2>
        <p>We retain personal information for as long as reasonably necessary to provide our services, maintain your account, fulfill contractual obligations, resolve disputes, enforce our agreements, and comply with legal requirements.</p>
        <p>Different types of information may be retained for different periods.</p>
        <p>For example:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Account information may be retained while your account remains active and for a reasonable period afterward.</li>
            <li>Transaction and billing records may be retained for applicable accounting, tax, and legal requirements.</li>
            <li>Support correspondence may be retained for as long as needed to resolve issues and maintain appropriate records.</li>
            <li>Analytics information may be retained according to the configuration and retention practices of the relevant analytics services.</li>
            <li>Community content may remain available while posted or as otherwise permitted by the applicable platform features and policies.</li>
        </ul>
        <p>When personal information is no longer necessary, we will delete it, anonymize it, or otherwise handle it in accordance with applicable law.</p>

        {{-- 8 --}}
        <h2 class="text-xl font-semibold mt-8">8. Data Security</h2>
        <p>We take reasonable technical and organizational measures designed to protect personal information against unauthorized access, disclosure, alteration, loss, and misuse.</p>
        <p>These measures may include access controls, secure authentication practices, protected password storage, and appropriate safeguards provided by our technology and payment service providers.</p>
        <p>However, no website, internet transmission, or electronic storage system can be guaranteed to be completely secure.</p>
        <p>You are responsible for keeping your password confidential and notifying us if you suspect unauthorized access to your account.</p>

        {{-- 9 --}}
        <h2 class="text-xl font-semibold mt-8">9. Cookies and Similar Technologies</h2>
        <p>We may use cookies and similar technologies to:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Keep the website functioning properly.</li>
            <li>Maintain login sessions.</li>
            <li>Remember relevant preferences.</li>
            <li>Analyze website traffic and feature usage.</li>
            <li>Diagnose technical problems and improve performance.</li>
        </ul>
        <p>Some cookies may be provided by third-party services.</p>
        <p>Where required, we will provide appropriate cookie notices and controls so you can accept or reject non-essential cookies.</p>
        <p>You may also be able to manage cookies through your browser settings. Disabling certain cookies may affect website functionality.</p>

        {{-- 10 --}}
        <h2 class="text-xl font-semibold mt-8">10. Children's Privacy</h2>
        <p>Kingsley Khord is an educational platform intended for musicians and piano learners of different skill levels.</p>
        <p>Individuals under 18 may use the website where permitted by applicable law and with parental or legal guardian consent when required.</p>
        <p>Parents or legal guardians should supervise minors' use of the platform, particularly participation in community features and the sharing of images, videos, or other personal information.</p>
        <p>We do not knowingly collect personal information from children in circumstances where such collection is prohibited by law.</p>
        <p>If you believe a child has provided personal information in violation of applicable legal requirements, please contact us at <a href="mailto:admin@kingsleykhordpiano.com" class="text-blue-600 hover:underline">admin@kingsleykhordpiano.com</a>. We will investigate and take appropriate action as required by law.</p>

        {{-- 11 --}}
        <h2 class="text-xl font-semibold mt-8">11. Your Privacy Rights</h2>
        <p>Depending on your location and applicable law, you may have the right to:</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Request access to your personal information.</li>
            <li>Request correction of inaccurate or incomplete information.</li>
            <li>Request deletion of personal information.</li>
            <li>Request restrictions on certain processing activities.</li>
            <li>Object to certain processing activities.</li>
            <li>Request a copy of certain personal information in a portable format.</li>
            <li>Withdraw consent where processing is based on consent.</li>
            <li>Lodge a complaint with the relevant data protection authority.</li>
        </ul>
        <p>These rights are subject to applicable legal limitations and exceptions.</p>
        <p>To exercise a privacy right, contact <a href="mailto:admin@kingsleykhordpiano.com" class="text-blue-600 hover:underline">admin@kingsleykhordpiano.com</a>. We may request reasonable information to verify your identity before responding.</p>
        <p>We will respond within the time required by applicable law.</p>

        {{-- 12 --}}
        <h2 class="text-xl font-semibold mt-8">12. Third-Party Websites and Services</h2>
        <p>Our website may contain links to, or integrate with, third-party services, including payment providers.</p>
        <p>We are not responsible for the independent privacy practices of third-party websites or services that we do not control.</p>
        <p>We encourage you to review their applicable privacy policies before providing information directly to them.</p>

        {{-- 13 --}}
        <h2 class="text-xl font-semibold mt-8">13. Changes to This Privacy Policy</h2>
        <p>We may update this Privacy Policy to reflect changes to our services, technology, business practices, or legal obligations.</p>
        <p>When we make changes, we will update the Effective Date at the top of this document. Where required by law, we will provide additional notice or obtain consent before implementing material changes.</p>
        <p>Your continued use of the website after an update does not override any consent requirements or other rights you have under applicable law.</p>

        {{-- 14 --}}
        <h2 class="text-xl font-semibold mt-8">14. Contact Us</h2>
        <p>For questions about this Privacy Policy, your personal information, or your privacy rights, contact:</p>
        <p>
            KingsleyKhordpiano, LLC<br>
            Email: <a href="mailto:admin@kingsleykhordpiano.com" class="text-blue-600 hover:underline">admin@kingsleykhordpiano.com</a><br>
            Website: <a href="https://KingsleyKhordpiano.com" class="text-blue-600 hover:underline">https://KingsleyKhordpiano.com</a>
        </p>
        <p>Please include enough information for us to understand and respond to your request, but do not send your password or complete payment card details.</p>
    </div>
</div>
@endsection