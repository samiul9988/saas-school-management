<style>
    .id-card { width: 324px; height: 204px; position: relative; overflow: hidden; border-radius: 8px; box-sizing: border-box; font-family: sans-serif; border: 1px solid #ccc; }

    /* Template 1 */
    .tpl1 .strip { position: absolute; left: 0; top: 0; bottom: 0; width: 16px; background: #3d1540; }
    .tpl1 .strip span { display: block; writing-mode: vertical-rl; transform: rotate(180deg); color: #fff; font-size: 7px; font-weight: bold; letter-spacing: 1px; text-align: center; height: 100%; padding: 6px 0; }
    .tpl1 .head { margin-left: 16px; background: linear-gradient(90deg, #4a1942, #6d2666); color: #fff; padding: 6px 10px; text-align: center; }
    .tpl1 .head .sname { font-size: 10.5px; font-weight: bold; line-height: 1.15; }
    .tpl1 .head .saddr { font-size: 6.5px; opacity: .9; margin-top: 1px; }
    .tpl1 .body { margin-left: 16px; display: table; width: calc(100% - 16px); padding: 8px 10px; box-sizing: border-box; }
    .tpl1 .photo-cell { display: table-cell; width: 64px; vertical-align: top; }
    .tpl1 .photo { width: 58px; height: 68px; border: 2px solid #4a1942; object-fit: cover; background: #eee; }
    .tpl1 .info-cell { display: table-cell; vertical-align: top; padding-left: 8px; }
    .tpl1 .sname-student { font-weight: bold; font-size: 11px; color: #222; margin-bottom: 3px; }
    .tpl1 .info-row { font-size: 8px; color: #333; margin-bottom: 1.5px; }
    .tpl1 .info-row .lbl { display: inline-block; width: 42px; color: #555; }

    /* Template 2 */
    .tpl2 { background: linear-gradient(180deg, #38bdf8, #0284c7); color: #fff; text-align: center; }
    .tpl2 .seal { position: absolute; top: 8px; left: 10px; width: 22px; height: 22px; border-radius: 50%; background: #fff; }
    .tpl2 .photo-wrap { margin-top: 14px; }
    .tpl2 .photo { width: 58px; height: 58px; border-radius: 6px; border: 2px solid #fff; object-fit: cover; background: #ddd; }
    .tpl2 .sname-student { font-weight: bold; font-size: 11.5px; margin-top: 4px; }
    .tpl2 .info-list { text-align: left; display: inline-block; margin-top: 6px; font-size: 7.5px; }
    .tpl2 .info-row { margin-bottom: 1.5px; }
    .tpl2 .info-row .lbl { display: inline-block; width: 68px; }

    /* Back side (both templates share this layout) */
    .id-card-back { text-align: center; padding-top: 10px; box-sizing: border-box; background: #fff; }
    .id-card-back .tag { font-size: 8px; font-weight: bold; color: #333; letter-spacing: .5px; }
    .id-card-back .seal-big { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #4a1942; margin: 10px auto 6px auto; }
    .id-card-back .sname { font-size: 10px; font-weight: bold; color: #222; }
    .id-card-back .saddr { font-size: 7px; color: #666; margin-top: 2px; }
    .id-card-back .sphone { font-size: 7px; color: #666; }
    .id-card-back .sign { margin-top: 20px; font-size: 7px; color: #333; }
    .id-card-back .sign .line { width: 100px; border-top: 1px solid #333; margin: 0 auto 3px auto; }
    .id-card-back .qr { margin-top: 8px; }
    .id-card-back .qr img { width: 46px; height: 46px; }

    .cards-grid { width: 100%; }
    .cards-grid td { padding: 6px; }
</style>
