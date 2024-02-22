<section class="my-product-discounts">
    <div id="product-discount-progress" class="product-discount-progress">
        <div class="discount-table">
            <table>
                <tbody>
                {foreach $grouped_awards item=awards}
                     <tr>
                        <th>{$awards[0].name}</th>
                    
                     {foreach from=$awards item=award}
                        <td data-price="{$award.price}" class="pricethreshold column column--1 discount--header">{$award.effect}</td>
                    {/foreach}    
                    </tr>
                {/foreach}
                    <tr class="first-row">
                        <th>{$cartPriceLabel}</th>
                        {foreach from=$priceThresholds item=priceThreshold}
                            <td data-price="{$priceThreshold.price}" class="pricethreshold column column--1 discount--header">{Tools::displayPrice($priceThreshold.price, Context::getContext()->currency)}</td>
                        {/foreach}
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div>Kwota w koszyku + na karcie produktu: <span id="cart_and_product_price">{$cart_price}</span></div>
</section>

<script>

   setInterval(() => {
        let x = $('#pp-product-price').text();
        x = parseFloat(x.replace(',', '.'));       
        x += {$cart_price};      

        $('#cart_and_product_price').text(x);

        // Znajdź wszystkie elementy z klasą .pricethreshold i iteruj przez nie
        $('.pricethreshold').each(function() {
            // Pobierz wartość atrybutu data-price i przekształć ją na liczbę
            var price = Number($(this).data('price'));

            // Sprawdź, czy price jest większe od x
            if (price > x) {
                // Jeśli tak, usuń klasę gained-award
                $(this).removeClass('gained-award');
            } else {
                // Jeśli nie, dodaj klasę gained-award
                $(this).addClass('gained-award');
            }
        });

   }, 500);
</script>