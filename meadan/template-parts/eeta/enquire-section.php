<?php
$heading         = get_sub_field( 'heading' );
$subheading      = get_sub_field( 'subheading' );
$intro_text      = get_sub_field( 'intro_text' );
$benefits        = get_sub_field( 'benefits' );
$disclaimer_text = get_sub_field( 'disclaimer_text' );
$form_id         = get_sub_field( 'form_id' );
$bg_image        = get_sub_field( 'bg_image' );
$bg_style        = $bg_image ? ' style="background-image:url(' . esc_url( $bg_image['url'] ) . ');"' : '';
?>
<section class="eeta-enquire" id="enquire">
    <div class="eeta-enquire__copy">
        <div class="eeta-enquire__copy-inner">
            <?php if ( $heading ) : ?>
                <h2 class="eeta-enquire__heading"><?php echo esc_html( $heading ); ?></h2>
            <?php endif; ?>
            <?php if ( $subheading ) : ?>
                <p class="eeta-enquire__subheading"><?php echo esc_html( $subheading ); ?></p>
            <?php endif; ?>
            <?php if ( $intro_text ) : ?>
                <p class="eeta-enquire__intro"><?php echo esc_html( $intro_text ); ?></p>
            <?php endif; ?>
            <?php if ( $benefits ) : ?>
                <ul class="eeta-enquire__benefits">
                    <?php foreach ( $benefits as $benefit ) : ?>
                        <?php if ( ! empty( $benefit['benefit_text'] ) ) : ?>
                            <li class="eeta-enquire__benefit">
                                <span class="eeta-enquire__check" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 6L5 9L10 3" stroke="#00A651" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <?php echo esc_html( $benefit['benefit_text'] ); ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ( $disclaimer_text ) : ?>
                <p class="eeta-enquire__disclaimer"><?php echo esc_html( $disclaimer_text ); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="eeta-enquire__form-panel"<?php echo $bg_style; ?>>
        <div class="eeta-enquire__form-container">
            <style>
                .eeta-enquire__form { width: 100%; }
                .eeta-enquire__form-row { display: flex; gap: 16px; margin-bottom: 20px; }
                .eeta-enquire__form-row--half .eeta-enquire__form-field { flex: 1; }
                .eeta-enquire__form-field { display: flex; flex-direction: column; width: 100%; }
                .eeta-enquire__label {
                    color: #00595a;
                    font-size: 12px;
                    font-weight: 600;
                    letter-spacing: 0.06em;
                    text-transform: uppercase;
                    margin-bottom: 6px;
                    font-family: 'Inter', sans-serif;
                }
                .eeta-enquire__input {
                    background: #ffffff;
                    border: 1px solid #dbe6e6;
                    border-radius: 4px;
                    color: #1c1c1c;
                    font-size: 15px;
                    font-family: 'Inter', sans-serif;
                    padding: 12px 14px;
                    outline: none;
                    transition: border-color 0.2s;
                    width: 100%;
                    box-sizing: border-box;
                    appearance: none;
                    -webkit-appearance: none;
                }
                .eeta-enquire__input::placeholder { color: #a0b0b0; }
                .eeta-enquire__input:focus { border-color: #00595a; box-shadow: 0 0 0 2px rgba(0,89,90,0.12); }
                .eeta-enquire__select {
                    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2300595a' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
                    background-repeat: no-repeat;
                    background-position: right 14px center;
                    padding-right: 38px;
                    cursor: pointer;
                    background-color: #ffffff;
                }
                .eeta-enquire__select option { background: #fff; color: #1c1c1c; }
                .eeta-enquire__textarea { resize: vertical; min-height: 120px; }
                .eeta-enquire__submit {
                    background: #00ef97;
                    border: none;
                    border-radius: 4px;
                    color: #002263;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 700;
                    font-family: 'Inter', sans-serif;
                    letter-spacing: 0.08em;
                    padding: 14px 36px;
                    text-transform: uppercase;
                    transition: background 0.2s, transform 0.1s;
                }
                .eeta-enquire__submit:hover { background: #00d488; }
                .eeta-enquire__submit:active { transform: scale(0.98); }
                @media (max-width: 600px) {
                    .eeta-enquire__form-row--half { flex-direction: column; }
                    .eeta-enquire__submit { width: 100%; }
                }
            </style>
            <form class="eeta-enquire__form" method="post" action="#">
                <div class="eeta-enquire__form-row eeta-enquire__form-row--half">
                    <div class="eeta-enquire__form-field">
                        <label class="eeta-enquire__label" for="enquire-first-name">First Name</label>
                        <input class="eeta-enquire__input" type="text" id="enquire-first-name" name="first_name" placeholder="Jane" required>
                    </div>
                    <div class="eeta-enquire__form-field">
                        <label class="eeta-enquire__label" for="enquire-last-name">Last Name</label>
                        <input class="eeta-enquire__input" type="text" id="enquire-last-name" name="last_name" placeholder="Smith" required>
                    </div>
                </div>
                <div class="eeta-enquire__form-row eeta-enquire__form-row--half">
                    <div class="eeta-enquire__form-field">
                        <label class="eeta-enquire__label" for="enquire-email">Email</label>
                        <input class="eeta-enquire__input" type="email" id="enquire-email" name="email" placeholder="jane@example.com" required>
                    </div>
                    <div class="eeta-enquire__form-field">
                        <label class="eeta-enquire__label" for="enquire-phone">Phone</label>
                        <input class="eeta-enquire__input" type="tel" id="enquire-phone" name="phone" placeholder="+61 4xx xxx xxx">
                    </div>
                </div>
                <div class="eeta-enquire__form-row">
                    <div class="eeta-enquire__form-field">
                        <label class="eeta-enquire__label" for="enquire-organisation">Organisation</label>
                        <input class="eeta-enquire__input" type="text" id="enquire-organisation" name="organisation" placeholder="Your organisation">
                    </div>
                </div>
                <div class="eeta-enquire__form-row">
                    <div class="eeta-enquire__form-field">
                        <label class="eeta-enquire__label" for="enquire-enquiry-type">Enquiry Type</label>
                        <select class="eeta-enquire__input eeta-enquire__select" id="enquire-enquiry-type" name="enquiry_type" required>
                            <option value="" disabled selected>Select enquiry type&hellip;</option>
                            <option value="individual">Individual</option>
                            <option value="employer">Employer</option>
                            <option value="community">Community</option>
                        </select>
                    </div>
                </div>
                <div class="eeta-enquire__form-row">
                    <div class="eeta-enquire__form-field">
                        <label class="eeta-enquire__label" for="enquire-message">Message</label>
                        <textarea class="eeta-enquire__input eeta-enquire__textarea" id="enquire-message" name="message" placeholder="Tell us how we can help&hellip;" rows="5"></textarea>
                    </div>
                </div>
                <div class="eeta-enquire__form-row">
                    <button class="eeta-enquire__submit" type="submit">Submit Enquiry</button>
                </div>
            </form>
        </div>
    </div>
</section>
