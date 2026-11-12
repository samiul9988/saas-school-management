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

    /* Template 2 — sky-blue framed card, white content area */
    .tpl2 { background: #0ea5e9; padding: 6px; box-sizing: border-box; }
    .tpl2 .inner { background: #fff; height: 190px; border-radius: 4px; box-sizing: border-box; padding: 6px 10px; text-align: center; position: relative; }
    .tpl2 .seal { width: 28px; height: 28px; border-radius: 50%; background: #fff; border: 2px solid #0ea5e9; overflow: hidden; margin: 0 auto; }
    .tpl2 .seal img { width: 100%; height: 100%; }
    .tpl2 .sname-top { font-size: 10px; font-weight: bold; color: #0369a1; padding: 3px 8px 0 8px; line-height: 1.15; text-transform: uppercase; }
    .tpl2 .divider { width: 70%; border-top: 1px solid #0ea5e9; margin: 4px auto; }
    .tpl2 .photo-wrap { margin-top: 2px; height: 56px; }
    .tpl2 .photo { display: block; width: 46px; height: 54px; border-radius: 4px; border: 2px solid #0ea5e9; background: #ddd; margin: 0 auto; }
    .tpl2 .sname-student { font-weight: bold; font-size: 11px; color: #0369a1; margin-top: 4px; text-transform: uppercase; clear: both; }
    .tpl2 .info-list { text-align: left; display: block; margin-top: 6px; font-size: 7.5px; color: #222; padding-left: 6px; }
    .tpl2 .info-row { margin-bottom: 1.5px; }
    .tpl2 .info-row .lbl { display: inline-block; width: 66px; color: #555; }
    .tpl2 .sign { position: absolute; right: 10px; bottom: 4px; font-size: 6.5px; color: #333; text-align: center; }
    .tpl2 .sign img { height: 16px; display: block; margin: 0 auto; }
    .tpl2 .sign .line { width: 60px; border-top: 1px solid #333; margin: 0 auto 2px auto; }

    /* Back side — Template 1's original design */
    .id-card-back { text-align: center; padding-top: 10px; box-sizing: border-box; background: #fff; }
    .id-card-back .tag { font-size: 8px; font-weight: bold; color: #333; letter-spacing: .5px; }
    .id-card-back .seal-big { width: 40px; height: 40px; border-radius: 50%; border: 2px solid #0284c7; margin: 10px auto 6px auto; overflow: hidden; }
    .id-card-back .seal-big img { width: 100%; height: 100%; object-fit: cover; }
    .id-card-back .sname { font-size: 10px; font-weight: bold; color: #222; }
    .id-card-back .saddr { font-size: 7px; color: #666; margin-top: 2px; }
    .id-card-back .sphone { font-size: 7px; color: #666; }
    .id-card-back .sign { margin-top: 20px; font-size: 7px; color: #333; }
    .id-card-back .sign .line { width: 100px; border-top: 1px solid #333; margin: 0 auto 3px auto; }
    .id-card-back .qr { margin-top: 8px; }
    .id-card-back .qr img { width: 46px; height: 46px; }

    /* Back side — Template 2, framed to match the front */
    .tpl2-back { background: #0ea5e9; padding: 6px; box-sizing: border-box; }
    .tpl2-back .inner { background: #fff; height: 190px; border-radius: 4px; box-sizing: border-box; padding: 8px 10px; text-align: center; position: relative; }
    .tpl2-back .tag { font-size: 8px; font-weight: bold; color: #0369a1; letter-spacing: .5px; text-transform: uppercase; }
    .tpl2-back .seal-big { width: 32px; height: 32px; border-radius: 50%; border: 2px solid #0ea5e9; margin: 6px auto 4px auto; overflow: hidden; }
    .tpl2-back .seal-big img { width: 100%; height: 100%; }
    .tpl2-back .sname { font-size: 10px; font-weight: bold; color: #0369a1; }
    .tpl2-back .saddr { font-size: 7px; color: #555; margin-top: 2px; }
    .tpl2-back .sphone { font-size: 7px; color: #555; }
    .tpl2-back .qr-cell { position: absolute; left: 10px; bottom: 6px; }
    .tpl2-back .qr-cell img { width: 40px; height: 40px; }
    .tpl2-back .validity-cell { position: absolute; right: 10px; bottom: 6px; text-align: right; font-size: 7px; color: #0369a1; font-weight: bold; }

    .cards-grid { width: 100%; }
    .cards-grid td { padding: 6px; }
</style>
