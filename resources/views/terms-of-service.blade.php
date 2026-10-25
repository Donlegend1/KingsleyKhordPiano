@extends('layouts.app') {{-- Or use 'layouts.member' if applicable --}}

@section('title', 'Terms of Service')

@section('content')
<section class="bg-white text-gray-800 dark:bg-gray-900 dark:text-white pt-28 pb-12 px-4">
    <div class="max-w-5xl mx-auto space-y-8">
        <h1 class="text-3xl font-bold border-b pb-4">Terms of Service</h1>

        <div class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
            <p><strong>Website:</strong> KingsleyKhordpiano.com</p>
            <p><strong>Company:</strong> KingsleyKhordpiano, LLC</p>
            <p><strong>Effective Date:</strong> October 25, 2025</p>
            <p><strong>Contact Email:</strong> <a href="mailto:admin@kingsleykhordpiano.com" class="text-blue-600 underline">admin@kingsleykhordpiano.com</a></p>
        </div>

        <div class="space-y-4 leading-relaxed text-base">

            {{-- 1 --}}
            <h2 class="text-xl font-semibold mt-8">1. Agreement to These Terms</h2>
            <p>Welcome to Kingsley Khord.</p>
            <p>These Terms of Service ("Terms") govern your access to and use of KingsleyKhordpiano.com, an online piano learning platform operated by KingsleyKhordpiano, LLC, a Delaware limited liability company ("Kingsley Khord," "we," "us," or "our").</p>
            <p>Our platform provides piano courses, lessons, educational materials, MIDI files, plugins, subscription memberships, and a community for musicians.</p>
            <p>By accessing our website, creating an account, purchasing a product, or subscribing to a membership plan, you agree to these Terms and our <a href="/privacy-policy" class="text-blue-600 underline">Privacy Policy</a>.</p>
            <p>If you do not agree to these Terms, you must discontinue your use of the website.</p>

            {{-- 2 --}}
            <h2 class="text-xl font-semibold mt-8">2. Eligibility and Accounts</h2>

            <h3 class="text-lg font-semibold">2.1 Eligibility</h3>
            <p>Kingsley Khord is available to users internationally, subject to applicable laws and any restrictions imposed by payment providers or other service providers.</p>
            <p>Individuals under 18 may use the platform where permitted by applicable law and with parental or legal guardian consent when required.</p>
            <p>A parent or legal guardian should review these Terms and supervise a minor's use of the platform where appropriate.</p>

            <h3 class="text-lg font-semibold">2.2 Account Registration</h3>
            <p>To create an account, you must provide the information requested during registration, including:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>Your name.</li>
                <li>A valid email address.</li>
                <li>A strong password.</li>
            </ul>
            <p>You agree to provide accurate information and update it when necessary.</p>
            <p>Members access their accounts using their registered email addresses and authentication credentials.</p>

            <h3 class="text-lg font-semibold">2.3 Account Security</h3>
            <p>You are responsible for maintaining the confidentiality of your login credentials and for activity conducted through your account, subject to applicable law.</p>
            <p>You must notify us promptly if you suspect unauthorized access to your account.</p>
            <p>You may not attempt to gain unauthorized access to another user's account or to restricted areas of the platform.</p>

            {{-- 3 --}}
            <h2 class="text-xl font-semibold mt-8">3. Membership Plans</h2>
            <p>Kingsley Khord offers paid memberships through two subscription plans: Standard and Premium.</p>
            <p>Memberships are available on monthly and annual billing schedules, as offered on the website.</p>
            <p>The price, billing frequency, and included benefits will be displayed at the time of purchase.</p>

            <h3 class="text-lg font-semibold">3.1 Standard Membership</h3>
            <p>The Standard plan includes access to the following benefits, subject to availability and the current membership offering:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>Roadmaps for all skill levels.</li>
                <li>Ear Training Quiz.</li>
                <li>Songs and MIDI transcriptions.</li>
                <li>Downloadable resources.</li>
                <li>Exclusive materials.</li>
                <li>Supportive member community.</li>
            </ul>

            <h3 class="text-lg font-semibold">3.2 Premium Membership</h3>
            <p>The Premium plan includes the benefits of the Standard plan, together with additional features that may include:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>Personalized practice plans.</li>
                <li>Feedback on your playing.</li>
                <li>An accountability framework.</li>
                <li>Monthly live workshops.</li>
                <li>In-depth master classes.</li>
            </ul>
            <p>The availability and delivery format of particular benefits may depend on the current program and the information displayed at the time of purchase.</p>

            <h3 class="text-lg font-semibold">3.3 Membership Access</h3>
            <p>Purchasing an individual digital product does not automatically grant membership access.</p>
            <p>To become a member, you must subscribe to either the Standard or Premium membership plan.</p>
            <p>Depending on the plan and current offering, an active membership may include access to certain digital products without a separate purchase.</p>
            <p>Membership benefits are provided according to the plan you select. Standard and Premium memberships do not necessarily include identical benefits.</p>

            {{-- 4 --}}
            <h2 class="text-xl font-semibold mt-8">4. Pricing, Currency, and Payments</h2>
            <p>Memberships and digital products are sold for the prices displayed on the website at the time of purchase.</p>
            <p>Payments may be offered in United States dollars (USD) or euros (EUR), depending on the product, payment option, and checkout configuration.</p>
            <p>We use third-party payment providers, including Stripe and PayPal, to process payments.</p>
            <p>By completing a purchase, you authorize the applicable payment provider to process the transaction according to your selected payment method and the relevant payment terms.</p>
            <p>You are responsible for reviewing the price, currency, subscription period, and other purchase details before confirming a transaction.</p>
            <p>We may change prices for future purchases or renewal periods, subject to applicable law and any notice requirements.</p>
            <p>Where required, we will notify existing subscribers of price changes before they take effect.</p>

            {{-- 5 --}}
            <h2 class="text-xl font-semibold mt-8">5. Recurring Subscriptions and Automatic Renewal</h2>
            <p>Kingsley Khord memberships operate on a recurring subscription basis.</p>
            <p>Unless you cancel your subscription before the next renewal, your membership will automatically renew at the applicable recurring price and billing interval.</p>
            <p>For example, a monthly subscription generally renews monthly, while an annual subscription generally renews annually.</p>
            <p>By subscribing, you authorize recurring charges through your selected payment method until you cancel.</p>

            <h3 class="text-lg font-semibold">5.1 No Free Trials</h3>
            <p>Kingsley Khord does not offer free trials.</p>
            <p>A membership subscription is a paid service, and you may be charged when you complete your subscription purchase.</p>
            <p>You should review the plan details and payment information before subscribing.</p>

            <h3 class="text-lg font-semibold">5.2 Subscription Management</h3>
            <p>You may manage and cancel your subscription through your account when the relevant cancellation functionality is available.</p>
            <p>If you cannot cancel through your account, you may contact <a href="mailto:admin@kingsleykhordpiano.com" class="text-blue-600 underline">admin@kingsleykhordpiano.com</a> for assistance.</p>
            <p>We will process cancellation requests in accordance with these Terms and applicable law.</p>

            <h3 class="text-lg font-semibold">5.3 Effect of Cancellation</h3>
            <p>Cancellation takes effect immediately.</p>
            <p>Your membership access may therefore end when the cancellation takes effect, subject to any rights that cannot lawfully be excluded.</p>
            <p>Cancellation prevents future subscription renewals but does not automatically entitle you to a refund for a previous payment.</p>
            <p>Refund eligibility is explained in Section 7.</p>

            {{-- 6 --}}
            <h2 class="text-xl font-semibold mt-8">6. Digital Products</h2>
            <p>Kingsley Khord offers individual digital products, which may include:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>Piano lessons and courses.</li>
                <li>MIDI files and transcriptions.</li>
                <li>Plugins.</li>
                <li>Downloadable resources.</li>
                <li>Other music-learning materials.</li>
            </ul>
            <p>The price and applicable access conditions for each product will be displayed before purchase.</p>
            <p>Unless expressly stated otherwise, purchasing an individual digital product grants access to that product only. It does not grant access to a paid membership plan.</p>
            <p>A membership may, depending on the selected plan, provide access to certain products that would otherwise be sold separately.</p>
            <p>Digital product purchases and membership subscriptions are separate transactions unless expressly combined at checkout.</p>

            {{-- 7 --}}
            <h2 class="text-xl font-semibold mt-8">7. Refund Policy</h2>
            <p>Kingsley Khord does not offer free trials and generally does not provide refunds for membership subscriptions or digital product purchases.</p>
            <p>However, refunds may be considered in the following circumstances:</p>
            <ol class="list-decimal pl-6 space-y-1">
                <li><strong>Duplicate payment:</strong> You were charged more than once for the same purchase.</li>
                <li><strong>Website technical glitch:</strong> A technical error attributable to our website resulted in an incorrect charge or another qualifying payment problem.</li>
            </ol>
            <p>Refunds remain subject to verification and applicable law. Nothing in this policy excludes a refund or other remedy that must be provided under applicable law.</p>

            <h3 class="text-lg font-semibold">7.1 How to Request a Refund</h3>
            <p>To request a refund, email <a href="mailto:admin@kingsleykhordpiano.com" class="text-blue-600 underline">admin@kingsleykhordpiano.com</a> with:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>The email address associated with your Kingsley Khord account.</li>
                <li>A screenshot showing the relevant transaction or payment issue.</li>
                <li>A brief explanation of the problem.</li>
            </ul>
            <p>Please do not include your password or complete payment card details.</p>
            <p>We may request additional reasonable information when necessary to verify the transaction.</p>

            <h3 class="text-lg font-semibold">7.2 Refund Processing Time</h3>
            <p>Once a refund has been approved and initiated, we expect the refunded amount to reach your bank account or relevant payment account within 3 to 5 business days.</p>
            <p>Actual processing times may depend on your bank, card issuer, payment provider, or other financial institution. We cannot guarantee the processing time of institutions outside our control.</p>
            <p>Where applicable law provides additional refund rights, those rights will apply.</p>

            {{-- 8 --}}
            <h2 class="text-xl font-semibold mt-8">8. Intellectual Property Ownership</h2>
            <p>All Kingsley Khord website content, educational materials, and digital products are owned by KingsleyKhordpiano, LLC or used under appropriate authorization.</p>
            <p>This includes, where applicable:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>Piano lessons and course videos.</li>
                <li>Written educational materials.</li>
                <li>Practice roadmaps.</li>
                <li>Ear-training exercises and quizzes.</li>
                <li>MIDI files and transcriptions.</li>
                <li>Downloadable resources.</li>
                <li>Plugins developed or distributed by Kingsley Khord.</li>
                <li>Graphics, logos, branding, and website design.</li>
                <li>Original recordings, arrangements, and other proprietary materials.</li>
                <li>Website software and platform features.</li>
                <li>Other original content made available through the platform.</li>
            </ul>
            <p>All applicable intellectual property rights are reserved.</p>
            <p>Purchasing a membership or digital product does not transfer ownership of the underlying content or intellectual property to you.</p>

            <h3 class="text-lg font-semibold">8.1 Limited Personal-Use License</h3>
            <p>Subject to these Terms and any additional license conditions presented with a product, Kingsley Khord grants you a limited, non-exclusive, non-transferable, revocable license to access and use purchased content and membership materials for your personal, non-commercial music education.</p>
            <p>You may use materials for your own learning and practice, within the limits of the applicable product license.</p>
            <p>This license does not grant you permission to redistribute, resell, sublicense, publicly republish, or commercially exploit Kingsley Khord's proprietary materials.</p>

            <h3 class="text-lg font-semibold">8.2 Prohibited Use of Content</h3>
            <p>Unless we provide express written permission or applicable law permits the activity, you may not:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>Resell or redistribute our courses, lessons, MIDI files, plugins, or downloadable resources.</li>
                <li>Reproduce or republish our proprietary content for commercial purposes.</li>
                <li>Share paid course materials with unauthorized individuals.</li>
                <li>Upload our proprietary materials to file-sharing platforms or unauthorized distribution websites.</li>
                <li>Record, copy, or distribute course videos without authorization.</li>
                <li>Make unauthorized downloads of lessons, videos, or other course content.</li>
                <li>Remove copyright notices or proprietary identifiers.</li>
                <li>Use our content to create or sell unauthorized competing copies of our materials.</li>
            </ul>
            <p>These restrictions apply regardless of whether the content was purchased individually or accessed through a membership.</p>

            <h3 class="text-lg font-semibold">8.3 No Commercial Rights by Default</h3>
            <p>Purchases from Kingsley Khord are for the personal educational use described in these Terms unless a separate written license expressly grants additional rights.</p>
            <p>Buying a product does not automatically grant commercial-use rights.</p>
            <p>If you require commercial rights, contact us to discuss whether an appropriate license is available.</p>

            {{-- 9 --}}
            <h2 class="text-xl font-semibold mt-8">9. Acceptable Use and Security</h2>
            <p>You agree to use the website lawfully and in accordance with these Terms.</p>
            <p>You must not:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>Hack, crack, or attempt to bypass the security of the website or its applications.</li>
                <li>Gain unauthorized access to accounts, systems, data, or restricted features.</li>
                <li>Circumvent authentication, access controls, or technical restrictions.</li>
                <li>Interfere with the normal operation, availability, or security of the platform.</li>
                <li>Upload malicious software or files intended to compromise systems.</li>
                <li>Use the platform in a manner that violates applicable law or infringes another person's rights.</li>
            </ul>

            <h3 class="text-lg font-semibold">9.1 Account Termination for Hacking or Cracking</h3>
            <p>Kingsley Khord's stated policy is to terminate accounts for hacking, cracking, or comparable unauthorized attempts to compromise or bypass the platform's security.</p>
            <p>Where we identify such conduct, we may restrict access, suspend the affected account, or terminate it, subject to applicable law.</p>
            <p>We will not treat ordinary technical problems, good-faith support requests, or accidental errors as hacking merely because a member reports them.</p>
            <p>Nothing in this section limits action that we are legally required or permitted to take to protect users, comply with the law, or address other serious security risks.</p>

            {{-- 10 --}}
            <h2 class="text-xl font-semibold mt-8">10. Community Guidelines and User-Uploaded Content</h2>
            <p>Kingsley Khord provides community features that allow members to interact and share permitted content.</p>
            <p>Depending on the available features, members may upload profile pictures, images, videos, and other files.</p>

            <h3 class="text-lg font-semibold">10.1 Your Responsibility for Uploaded Content</h3>
            <p>You are responsible for ensuring that content you upload or share:</p>
            <ul class="list-disc pl-6 space-y-1">
                <li>Belongs to you or is shared with appropriate authorization.</li>
                <li>Does not infringe another person's copyright, trademark, privacy, or other legal rights.</li>
                <li>Does not contain unlawful material.</li>
                <li>Does not contain malicious code or files designed to compromise the platform.</li>
                <li>Does not violate applicable law.</li>
            </ul>
            <p>Do not upload another person's personal information, images, or recordings without an appropriate legal basis or permission.</p>

            <h3 class="text-lg font-semibold">10.2 License to Host and Display Your Content</h3>
            <p>You retain ownership of content you own and upload, subject to any third-party rights that apply.</p>
            <p>By uploading content, you grant KingsleyKhordpiano, LLC a non-exclusive, worldwide, royalty-free license to host, store, reproduce, process, and display that content as reasonably necessary to operate the website and provide the community features you use.</p>
            <p>This license is limited to the purposes of operating, maintaining, securing, and providing the platform, including displaying content to the intended audience.</p>
            <p>The license ends when the content is deleted or your account is closed, except to the extent that continued retention or use is reasonably necessary for backups, security, legal compliance, dispute resolution, or other lawful purposes.</p>
            <p>You represent that you have the rights necessary to grant this license.</p>

            <h3 class="text-lg font-semibold">10.3 Content Visibility</h3>
            <p>Content shared in community areas may be visible to other members.</p>
            <p>Do not assume that community posts, images, or videos are private unless the platform explicitly identifies the relevant feature as private.</p>

            <h3 class="text-lg font-semibold">10.4 Content Removal</h3>
            <p>We may remove content when required by law or when reasonably necessary to address security threats, intellectual property concerns, or other legal obligations.</p>
            <p>Members who believe their intellectual property has been infringed may contact <a href="mailto:admin@kingsleykhordpiano.com" class="text-blue-600 underline">admin@kingsleykhordpiano.com</a> with relevant details.</p>

            {{-- 11 --}}
            <h2 class="text-xl font-semibold mt-8">11. Educational Disclaimer</h2>
            <p>Kingsley Khord provides educational resources intended to help musicians develop their piano-playing, listening, practice, and music-learning skills.</p>
            <p>We aim to provide useful and effective learning materials, but we do not guarantee that every member will achieve a particular skill level, examination result, performance outcome, or career result.</p>
            <p>Your progress depends on factors that may include your current ability, practice habits, consistency, learning environment, and individual circumstances.</p>
            <p>Our courses and membership benefits are educational services and should not be understood as a guarantee of specific results.</p>

            {{-- 12 --}}
            <h2 class="text-xl font-semibold mt-8">12. Website Availability and Changes to Services</h2>
            <p>We work to maintain a functional and accessible platform, but uninterrupted availability cannot be guaranteed.</p>
            <p>The website may occasionally be unavailable because of maintenance, technical failures, updates, security measures, or circumstances outside our control.</p>
            <p>We may update, improve, modify, or discontinue website features and educational materials as our platform develops.</p>
            <p>Where a change materially affects a paid service, we will handle the change in accordance with applicable law and any contractual obligations.</p>
            <p>Nothing in this section permits us to disregard mandatory consumer rights.</p>

            {{-- 13 --}}
            <h2 class="text-xl font-semibold mt-8">13. Third-Party Services</h2>
            <p>Our website may rely on third-party services, including Stripe and PayPal, to process transactions.</p>
            <p>Some features may also depend on third-party hosting, technology, or infrastructure providers.</p>
            <p>Your use of third-party services may be subject to their own terms and privacy policies.</p>
            <p>We are not responsible for independent services that we do not own or control, except to the extent responsibility cannot lawfully be excluded.</p>

            {{-- 14 --}}
            <h2 class="text-xl font-semibold mt-8">14. Limitation of Liability</h2>
            <p>To the maximum extent permitted by applicable law, KingsleyKhordpiano, LLC will not be liable for indirect, incidental, special, consequential, or punitive damages arising from your use of the website, including loss of profits, data, business opportunities, or anticipated results.</p>
            <p>To the maximum extent permitted by applicable law, our liability arising out of or relating to the website, a membership, or a digital product will be limited to the amount you paid to Kingsley Khord for the particular product or service giving rise to the claim during the 12 months preceding the event giving rise to the claim.</p>
            <p>These limitations do not apply where prohibited by law, including liability that cannot legally be excluded or limited.</p>
            <p>Nothing in these Terms removes any consumer rights that applicable law makes mandatory.</p>

            {{-- 15 --}}
            <h2 class="text-xl font-semibold mt-8">15. Indemnification</h2>
            <p>To the extent permitted by applicable law, you agree to be responsible for losses, liabilities, and reasonable expenses arising from your unlawful use of the website, your infringement of another person's rights, or your material violation of these Terms.</p>
            <p>This provision does not require you to indemnify Kingsley Khord for losses caused by our own unlawful conduct or where such an obligation would be prohibited by applicable law.</p>

            {{-- 16 --}}
            <h2 class="text-xl font-semibold mt-8">16. Governing Law</h2>
            <p>These Terms are governed by the laws of the State of Delaware, United States, without regard to conflict-of-law principles, except where applicable law requires otherwise.</p>
            <p>Nothing in this section deprives consumers of mandatory protections or rights available under the laws that apply to them.</p>
            <p>Any dispute will be handled by a court or other forum with appropriate jurisdiction, subject to applicable law.</p>

            {{-- 17 --}}
            <h2 class="text-xl font-semibold mt-8">17. Changes to These Terms</h2>
            <p>We may revise these Terms to reflect changes in our services, products, business practices, or legal obligations.</p>
            <p>When we make changes, we will update the Effective Date at the top of this document.</p>
            <p>Where required by law, we will provide advance notice or obtain consent before implementing material changes.</p>
            <p>Your continued use of the website following an update will be subject to applicable legal requirements and does not eliminate any consent rights you may have.</p>

            {{-- 18 --}}
            <h2 class="text-xl font-semibold mt-8">18. Severability</h2>
            <p>If any provision of these Terms is found to be invalid or unenforceable, the remaining provisions will continue to apply to the extent permitted by law.</p>
            <p>The invalid or unenforceable provision will be interpreted or modified only to the extent legally permissible to best reflect its original purpose.</p>

            {{-- 19 --}}
            <h2 class="text-xl font-semibold mt-8">19. Entire Agreement</h2>
            <p>These Terms and our Privacy Policy constitute the agreement governing your use of the website, subject to any additional terms expressly presented for particular products or services.</p>
            <p>If additional product-specific terms apply, those terms will govern the relevant product to the extent of any inconsistency, subject to applicable law.</p>

            {{-- 20 --}}
            <h2 class="text-xl font-semibold mt-8">20. Contact Information</h2>
            <p>For questions about these Terms, membership subscriptions, digital products, payments, cancellations, or refunds, contact:</p>
            <p>
                KingsleyKhordpiano, LLC<br>
                Email: <a href="mailto:admin@kingsleykhordpiano.com" class="text-blue-600 underline">admin@kingsleykhordpiano.com</a><br>
                Website: <a href="https://KingsleyKhordpiano.com" class="text-blue-600 underline">https://KingsleyKhordpiano.com</a>
            </p>
        </div>
    </div>
</section>
@endsection