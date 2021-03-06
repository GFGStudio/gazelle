<?php
declare(strict_types=1);

View::show_header('DMCA'); ?>

<h2>DMCA Information</h2>

<section class="tldr">
  <p>
    <em>If</em> you're a copyright owner or an agent of one,
    <em>and</em> you believe that user-generated content on the domain
    https://biotorrents.de infringes your copyrights:
    <em>then</em> you may notify our Digital Millennium Copyright Act (DMCA) agent in writing.
  </p>

  <ul class="p">
    <li>
      Identification of the copyrighted work you claim is infringed.
      <em>Please include your copyright registration number or proof of status pending.</em>
      Claims for U.S. works require registration.
    </li>

    <li>
      Identification of the material you claim is infringing.
      To speed up processing, please include:
      (1) the permalink <code>[PL]</code> URI, <em>and</em>
      (2) the BitTorrent <code>info_hash</code>.
    </li>

    <li>
      A statement that you have a good faith belief that the material's use in the manner you complain of
      isn't authorized by the copyright owner, its agent, or the law.
    </li>

    <li>
      A statement that the notification's information is accurate, and under penalty of perjury,
      that you're authorized to act on behalf of an allegedly infringed exclusive right.
    </li>


    <li>
      Your physical or electronic signature, or of someone authorized to act on your behalf.
    </li>

    <li>
      Information reasonably sufficient to permit us to contact you,
      such as an address, telephone number, and email.
    </li>
  </ul>

  <p>
    Because a high percentage of DMCA takedown notices are invalid or abusive,
    Omics Tools LLC reserves the right to ignore requests for unregistered works.
  </p>

  <?php
  /*
  <p>
    Circumstances that may delay processing, including not limited:
  </p>

  <ul class="p">
    <li>
      URI formulations that violate BioTorrents.de's normal access rules,
      e.g., unsecured HTTP, <em>or</em> requests that fail to identify specific pieces of UGC.
    </li>

    <li>
      Generic or boilerplate statements.
      Neither statement should contain passages with quoted online search results.
    </li>

    <li>
      Requests signed by other means than Ed25519 or RSA 4096,
      or encoded in other formats than UTF-8 or ASCII.
    </li>

    <li>
      PO boxes, addresses outside the U.S., or addresses that can't accept USPS Certified Mail.
      VoIP telephone numbers or numbers without a <code>+1</code> country code.
    </li>

    <li>
      Email servers that don't comply with at least two of:
      <a href="https://tools.ietf.org/html/rfc7208">RFC 7208 (SPF)</a>,
      <a href="https://tools.ietf.org/html/rfc8463">RFC 8463 (DKIM)</a>, and
      <a href="https://tools.ietf.org/html/rfc7489">RFC 7489 (DMARC)</a>.
      Requests from free mailboxes such as Gmail, ProtonMail, etc.
      Any email that violates
      <a href="https://www.law.cornell.edu/uscode/text/15/7704">15 USC 7704(a)</a>.
    </li>
  </ul>
  */ ?>

  <p>
    We'll expeditiously disable access to the targets of valid requests.
    Our agent to receive notifications of claimed infringement is:
  </p>

  <p>
    <strong>
      Address
    </strong>
    <br />

    Copyright Manager<br />
    Omics Tools LLC<br />
    30 N Gould St Ste 4000<br />
    Sheridan, WY 82801
  </p>

  <p>
    <strong>
      Email
    </strong>
    <br />
    dmca at biotorrents dot de
  </p>

  <p>
    Remember that under
    <a href="https://www.law.cornell.edu/uscode/text/17/512">17 USC 512(f)</a>,
    anyone who knowingly materially misrepresents infringement may be subject to liability.
    Also see
    <a href="https://www.law.cornell.edu/uscode/text/17/107">17 USC 107</a> and
    <a href="https://www.law.cornell.edu/uscode/text/17/108">17 USC 108</a>.
  </p>

  <p>
    Please use
    <a href="/sections/legal/pubkey.txt">GPG A1D095A5DEC74A8B</a>
    if you wish.
  </p>
</section>

<?php View::show_footer();
