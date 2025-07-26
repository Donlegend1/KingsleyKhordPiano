@extends('layouts.app') {{-- Or use 'layouts.member' if applicable --}}

@section('title', 'Terms of Service')

@section('content')
<section class="bg-white text-gray-800 dark:bg-gray-900 dark:text-white py-12 px-4">
    <div class="max-w-5xl mx-auto space-y-8">
        <h1 class="text-3xl font-bold border-b pb-4">Terms of Service</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Effective Date: </p>

        <div class="space-y-6 leading-relaxed text-base">
            <h2 class="text-xl font-semibold">1. Welcome to Kingsleykhord Piano Academy</h2>
            <p><strong>1.1 Introduction:</strong> Kingsleykhord Piano Academy (“Kingsleykhord,” “we,” “us,” or “our”) provides piano lessons, digital courses, tutorials, community features, and related services (collectively, the “Services”) through its website at kingsleykhordpiano.com (the “Site”), subject to these Terms of Use (“Terms”). Please read these Terms carefully—they govern your use of the Site and Services.</p>
            <p><strong>1.2 Changes to Terms:</strong> We may update these Terms at any time. We'll post updates on this page and note the revised date. Changes go into effect 14 days after posting (unless required immediately for legal or functional reasons). By continuing to use our Services after changes take effect, you agree to the modified Terms.</p>
            <p><strong>1.3 Privacy:</strong> Your privacy matters to us. Please review our <a href="/privacy-policy" class="text-blue-600 underline">Privacy Policy</a>. By using the Services, you consent to our use of your personal data as described there.</p>

            <h2 class="text-xl font-semibold mt-8">2. Access & Use of the Services</h2>
            <p><strong>2.1 Your License:</strong>  With your purchase or membership, we grant you a limited, non-exclusive, non-transferable license to access our content on a streaming-only basis, strictly for personal, non-commercial use. You agree not to publicly perform or share the content outside our platform. We may revoke your access at any time; upon revocation, please delete all downloaded materials</p>
            <p><strong>2.2 Registration & Accuracy:</strong>  To access certain features, you must register and provide accurate, current, and complete information. You’re responsible for maintaining the confidentiality of your account credentials. Notify us immediately of any unauthorized access.</p>
            <p><strong>2.3 Account Security:</strong> You’re responsible for all activity under your account. Do not share login details or let others use your account. We are not liable for any loss or damage from improper account usage.</p>
            <p><strong>2.4 Service Changes & Availability:</strong>  We may modify, suspend, or discontinue any part of the Service at any time without liability to you. We have no obligation to retain your data or submissions longer than required by law</p>
            <p><strong>2.5 Storage & Data Retention:</strong> We may set limits on file storage duration and volume. We’re not responsible for loss or deletion of your data.</p>
            <p><strong>2.6 Mobile Access:</strong> You may access our Services via mobile devices. Standard carrier charges and data rates will apply</p>

            <h2 class="text-xl font-semibold mt-8">3. Acceptable Use</h2>
            <p><strong>3.1 User Conduct:</strong>  
             You’re solely responsible for your own content (questions, comments, uploads). You agree not to:
             <ol>
              <li>
               ● Upload content that infringes IP rights, violates laws, or contains harmful code.
              </li>
              <li>
                ● Post abusive, hateful, pornographic, or illegal content
              </li>
              <li>
                ● Spam, harass, impersonate others, or solicit personal data from minors
              </li>

                <li>
                    ●  Disrupt the Site or attempt unauthorized access.
              </li>
             </ol>

                 Violation may lead to content removal, account suspension, or termination—and possible legal action.</p>

 
            <p><strong>3.2 Paid Services & Payment Terms:</strong>  If a fee is required for Services, you must provide valid payment info. Subscriptions auto-renew unless cancelled. Recurring payments will be processed until you cancel. Payment processors handle
                 your payment—it’s secure and external to us. Disputes must be reported within 60 days.</p>
            <p><strong>3.3 Refunds and Cancellations:</strong> Subscription fees are non-refundable. You may cancel at any time—access continues until the end of the current billing period. We reserve the right to offer refunds or credits at our discretion. To cancel, use your account dashboard or contact support</p>
            <p><strong>3.4 No Commercial Use:</strong> You may not use our materials commercially unless expressly authorized. All content is for personal learning and enjoyment.</p>

            <h2 class="text-xl font-semibold mt-8">4. Intellectual Property</h2>
            <p><strong>4.1 Our Content & Branding:</strong> All course materials, designs, trademarks (including site content and logos) belong to Kingsleykhord and its licensors. You may not reproduce, distribute, or alter them without permission</p>
            <p><strong>4.2 Third-Party Content:</strong> We are not liable for third-party content. We may remove any content that violates these Terms or is otherwise objectionable.</p>
            <p><strong>4.3 User Submissions:</strong>  By submitting content or ideas (“Submissions”), you grant us a non-exclusive, royalty-free license to use them in any form. You confirm you own such content and that it doesn’t infringe others’ rights.</p>
            <p><strong>4.4 Copyright Policy:</strong> 
            
             If you believe your copyright has been infringed, contact us with:
             <ol>
                <li>
                    ● Identification of the work and location on the Site
                </li>
                 <li>
                    ● Your contact information
                </li>
                 <li>
                    ● A statement of good-faith belief of infringement
                </li>
                 <li>
                    ● A statement, under penalty of perjury, that you are the owner
                </li>
                 <li>
                    ● Your signature
                </li>
             </ol>
                For counter-notices, similar conditions apply. We respect copyright law and may terminate repeat infringers.</p>

            <h2 class="text-xl font-semibold mt-8">5. Community & Third-Party Services</h2>
            <p><strong>5.1 Social Login & Integrations:</strong>  You may link our Services with social platforms. We’ll handle any shared data per our Privacy Policy. We’re not responsible for third-party privacy or content</p>
            <p><strong>5.2 Interactions with Others:</strong> You’re responsible for your communications with others. We may intervene in disputes or remove content that violates rules but aren’t required to.</p>

            <h2 class="text-xl font-semibold mt-8">6. Disclaimers & Liability</h2>
            <p><strong>6.1 No Warranties:</strong>  Our Services are provided “as-is” without guarantees. We don’t promise uninterrupted access or specific results.</p>
            <p><strong>6.2 Limitation of Liability:</strong> To the extent permitted by law, we’re not liable for indirect, special, or consequential damages. Our total liability is limited to what you’ve paid in the past six months.</p>

            <h2 class="text-xl font-semibold mt-8">7. Indemnity</h2>
            <p>You agree to defend and hold harmless Kingsleykhord and its affiliates against any claims arising from your use of the Services or violation of these Terms.</p>

            <h2 class="text-xl font-semibold mt-8">8. Governing Law & Disputes</h2>
            <p>These Terms are governed by Nigerian law. Disputes should first be addressed informally via contact@kingsleykhordpiano.com. If unresolved, conflicts go to individual arbitration under Nigerian law in Lagos, Nigeria. Arbitration is binding and not part of class actions. You or we may still pursue IP claims in court or small claims actions.</p>

            <h2 class="text-xl font-semibold mt-8">9. Termination</h2>
            <p>We may suspend or terminate your account for violations, abuse, or inactivity. Upon termination, you lose access to Services and content. We’re not liable for losses from termination.</p>

            <h2 class="text-xl font-semibold mt-8">10. General</h2>
            <p>These Terms are the full agreement. If any part is unenforceable, the rest remains valid. You may not assign your rights; we may. Notices may be sent via email or through the Site. No liability for delays caused by factors beyond our control.</p>

            <h2 class="text-xl font-semibold mt-8">11. Contact Us</h2>
            <p>Questions? Email us at <a href="mailto:contact@kingsleykhordpiano.com" class="text-blue-600 underline">contact@kingsleykhordpiano.com</a></p>
        </div>
    </div>
</section>
@endsection
