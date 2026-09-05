# Funkcionalnosti i poslovni tokovi

## Uloge

- **Kupac** pregleda proizvode, upravlja korpom, potvrđuje narudžbinu i vidi samo svoje narudžbine.
- **Zaposleni** upravlja proizvodima, skladištima, lotovima, resursima i obradom narudžbina.
- **Administrator** ima prava zaposlenog i pristup finansijskom izveštaju.

## Proizvodi i skladišta

Proizvodi, skladišta i skladišne lokacije mogu se označiti kao neaktivni. Tako istorijski podaci ostaju sačuvani, dok neaktivni zapisi više ne učestvuju u novim poslovnim procesima.

## Tok lota

Lot se kreira za sortu, parcelu, datum berbe i početnu količinu. Sistem mu automatski dodeljuje oznaku.

Uobičajeni tok statusa je:

```text
KREIRAN -> USKLADISTEN -> RASPOLOZIV -> ISCRPLJEN
```

Pre prodaje lot mora biti primljen na aktivnu skladišnu lokaciju i mora imati klasu kvaliteta. Lot može biti premešten, blokiran, odblokiran, korigovan ili povučen. Svaka poslovna promena ostaje zabeležena kao događaj sledljivosti.

## Tok narudžbine

Kupac potvrđuje sadržaj korpe, a sistem čuva tadašnju cenu i veličinu pakovanja. Zaposleni pokreće FIFO rezervaciju, koja bira najstarije odgovarajuće raspoložive lotove i rezerviše samo cela pakovanja.

Rezervacija se izvršava u transakciji uz zaključavanje redova. Otpremom rezervacije postaju izdate, a otkazivanjem se rezervisana količina vraća lotovima. Kada lot više nema raspoloživu količinu, dobija status `ISCRPLJEN`.

## Finansijski izveštaj

Administrator bira mesec i godinu. Izveštaj obuhvata narudžbine koje su stvarno otpremljene u tom mesecu, na osnovu događaja `KOLICINA_IZDATA`.

Prihod se računa iz cena sačuvanih u stavkama narudžbine. Rashod obuhvata mesečni trošak korišćenih skladišta i evidentirane resurse lotova iz kojih je roba izdata.
